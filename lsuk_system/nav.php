<?php //session_start(); 
include "permissmenuitem.php";
?>

<aside id="sidebar" class="column">


		<h3>Booking Forms</h3>
        <div style="margin-left:20px; margin-top:50px;">
		<ul id="menu-v">
<li><a href="#" onClick="MM_openBrWindow('interpreter.php','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><img src="images/arrow.gif" style="margin-left:-15px;" /> Interpreter</a></li>
			<li><a href="#" onClick="MM_openBrWindow('telephone.php','view','scrollbars=yes,resizable=yes,width=900,height=600')"><img src="images/arrow.gif" style="margin-left:-15px;" />Telephone Interpreter</a></li>
			<li><a href="#" onClick="MM_openBrWindow('translation.php','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><img src="images/arrow.gif" style="margin-left:-15px;" />Translation</a></li>
    <li><a href="#">Booking Record</a>
        <ul class="sub">
            <?php MenuPermiss::ItemUrlPage("interp_list.php","Interpreters List") ?>
            <?php MenuPermiss::ItemUrlPage("telep_list.php","Telephone List") ?>
            <?php MenuPermiss::ItemUrlPage("trans_list.php","Translation List") ?>

  <?php 
  if(MenuPermiss::HasPriv('Management'))
  {
        ?>
        <?php MenuPermiss::ItemUrlPageSpan("interp_list_rest.php",'Interpreters <span style="color:#F00">Trash</span> List'); ?>
        <?php MenuPermiss::ItemUrlPageSpan("telep_list_rest.php",'Telephone <span style="color:#F00">Trash</span> List'); ?>
        <?php MenuPermiss::ItemUrlPageSpan("trans_list_rest.php",'Translation <span style="color:#F00">Trash</span> List'); ?>
             
        <?php MenuPermiss::ItemUrlPageSpan("interp_list_cancel_rest.php",'Interpreters <span style="color:#F00">Order Cancel</span> List'); ?>
        <?php MenuPermiss::ItemUrlPageSpan("telep_list_cancel_rest.php",'Telephone <span style="color:#F00">Order Cancel</span> List'); ?>
        <?php MenuPermiss::ItemUrlPageSpan("trans_list_cancel_rest.php",'Translation <span style="color:#F00">Order Cancel</span> List'); ?>

        <?php MenuPermiss::ItemUrlPageSpan("interp_list_multip.php",'Interpreters Multip List'); ?>
        <?php MenuPermiss::ItemUrlPageSpan("telep_list_multip.php",'Telephone Multip List'); ?>
        <?php MenuPermiss::ItemUrlPageSpan("trans_list_multip.php",'Translation Multip List'); ?>

        <?php 
    } 
    ?>
			
        </ul>
    </li>
   
    <li><a href="#">Reports</a>
    <?php 
    if(MenuPermiss::HasPriv('Operator'))
    {
        ?>
        <ul class="sub">
     
        <?php MenuPermiss::ItemUrlPageSpan("rip_daily.php",'Daily Report (Allocated)'); ?>
        <?php MenuPermiss::ItemUrlPageSpan("rip_daily_unaloc.php",'Daily Report (Un-Allocated)'); ?>

        </ul>

        <?php 
    }
    
    if(MenuPermiss::HasPriv('Management') || MenuPermiss::HasPriv('Finance'))
    {
        ?>
        <ul class="sub">
        <?php //MenuPermiss::ItemHdrUrlPage("rip_interp.php",'Account Purposes'); ?>
        <!--<ul class="sub">-->

        <?php //MenuPermiss::ItemUrlPageSpan("rip_interp.php",'Interp Rep(Ac Purposes)'); ?>
        <?php //MenuPermiss::ItemUrlPageSpan("rip_telep.php",'Telep Interp Rep(Ac Purposes)'); ?>
        <?php //MenuPermiss::ItemUrlPageSpan("rip_trans.php",'Trans Interp Rep(Ac Purposes)'); ?>
        <!--</ul>-->
        </li>
        <?php MenuPermiss::ItemUrlPageSpan("rip_pending_all.php",'Pending  invoices (All)'); ?>
        <?php MenuPermiss::ItemUrlPageSpan("rip_interpreters_salary.php",'Interpreters Salary Report'); ?>
        <?php MenuPermiss::ItemUrlPageSpan("rip_interpreters_salary_paid.php",'Interpreters Paid Salary Report'); ?>
        <!--<ul class="sub">-->
        <?php //MenuPermiss::ItemUrlPageSpan("rip_comp_interp_12.php",'Interpreter Report (Comp)'); ?>
        <?php //MenuPermiss::ItemUrlPageSpan("rip_comp_interp_telep_12.php",'Telephone Report (Comp)'); ?>
        <?php //MenuPermiss::ItemUrlPageSpan("rip_comp_interp_trans_12.php",'Translation Report (Comp)'); ?>
        <!--</ul>-->
        </li>

        <?php// MenuPermiss::ItemHdrUrlPage("rip_interp_12.php",'Reports (Client)'); ?>
        <!--<ul class="sub">-->

        <?php// MenuPermiss::ItemUrlPageSpan("rip_interp_12.php",'Interpreter Report (Client)'); ?>
        <?php// MenuPermiss::ItemUrlPageSpan("rip_interp_telep_12.php",'Telephone Report (Client)'); ?>
        <?php// MenuPermiss::ItemUrlPageSpan("rip_interp_trans_12.php",'Translation Report (Client)'); ?>
        <!--</ul>-->
      </li>

        <?php MenuPermiss::ItemHdrUrlPage("rip_interp_12.php",'<span style="color:#F00">Consolidate Reports</span>'); ?>
          <ul class="sub">
        
        <?php MenuPermiss::ItemUrlPageSpan("rip_consolidate_monthly_all.php",'Cosolidated Comp-Wise(All)'); ?>
        <?php MenuPermiss::ItemUrlPageSpan("rip_consolidate.php",'Cosolidated Comp-Wise'); ?>
        <?php //MenuPermiss::ItemUrlPageSpan("rip_consolidate_telep.php",'Cosolidated Comp-Wise(telep)'); ?>
        <?php //MenuPermiss::ItemUrlPageSpan("rip_consolidate_trans.php",'Cosolidated Comp-Wise(trans)'); ?>
        <?php MenuPermiss::ItemUrlPageSpan("rip_consolidate_monthly.php",'Cosolidated Monthly(All)'); ?>
        <?php //MenuPermiss::ItemUrlPageSpan("rip_consolidate_monthly_telep.php",'Cosolidated Monthly(telep)'); ?>
        <?php //MenuPermiss::ItemUrlPageSpan("rip_consolidate_monthly_trans.php",'Cosolidated Monthly(trans)'); ?>
        <?php MenuPermiss::ItemUrlPageSpan("rip_consolidate_cancelled.php",'Order Cancel Monthly'); ?>
        <?php MenuPermiss::ItemUrlPageSpan("rip_consolidate_cancelled_telep.php",'Order Cancel Monthly(telep)'); ?>
        <?php MenuPermiss::ItemUrlPageSpan("rip_consolidate_cancelled_trans.php",'Order Cancel Monthly(trans)'); ?>


        </ul></li>
        <!--  <li><a href="rip_pending_inv_telep.php">Pending  invoices (Telep)</a></li>
        <li><a href="rip_pending_inv_trans.php">Pending  invoices (Trans)</a></li>-->
        <?php //MenuPermiss::ItemUrlPageSpan("rip_pending_payment_12.php",'Pending  Payment Report'); ?>

        <!--  <li><a href="rip_paid_inv.php">Paid  invoices (Intrp)</a></li>
        <li><a href="rip_paid_inv_telep.php">Paid  invoices (Telep)</a></li>
        <li><a href="rip_paid_inv_trans.php">Paid  invoices (Trans)</a></li>-->
        <?php MenuPermiss::ItemUrlPageSpan("rip_paid_all.php",'Paid  invoices (All)'); ?>
  
        <?php// MenuPermiss::ItemHdrUrlPage("rip_lang_interp.php",'Language Reports'); ?>
        <!--<ul class="sub">-->
            <?php// MenuPermiss::ItemUrlPageSpan("rip_lang_interp.php",'Language Report (Interp)'); ?>

            <?php //MenuPermiss::ItemUrlPageSpan("rip_lang_telep.php",'Language Report (Telep)'); ?>
            <?php// MenuPermiss::ItemUrlPageSpan("rip_lang_trans.php",'Language Report (Trans)'); ?>
            
        <!--</ul>-->
      </li>
        <?php MenuPermiss::ItemUrlPageSpan("rip_po.php",'Purchase Order Report'); ?>
        <?php MenuPermiss::ItemUrlPageSpan("rip_bo.php",'Business Order Report'); ?>
            <?php MenuPermiss::ItemUrlPageSpan("rip_summary.php",'Summary Report'); ?>
            <?php MenuPermiss::ItemUrlPageSpan("rip_daily.php",'Daily Report (Alocated)'); ?>
        <?php MenuPermiss::ItemUrlPageSpan("rip_daily_unaloc.php",'Daily Report (Un-Alocated)'); ?>
        <?php //MenuPermiss::ItemUrlPageSpan("rip_comp_exp.php",'Comp Expenses Report'); ?>
                
        <?php 
        if(MenuPermiss::HasPriv('Management'))
        {
            ?>
            <?php //MenuPermiss::ItemUrlPageSpan("rip_profit_loss.php",'Profit & Loss'); ?>
            <?php// MenuPermiss::ItemUrlPageSpan("rip_slp.php",'SLA Report'); ?>
 <?php MenuPermiss::ItemHdrUrlPage("rip_comp_interp_12.php",'Reports (Comp)'); ?>
        <ul class="sub">

        <?php MenuPermiss::ItemUrlPageSpan("rip_comp_interp_12.php",'Overall Report (Comp)'); ?>
        <?php //MenuPermiss::ItemUrlPageSpan("rip_comp_interp_12.php",'Interpreter Report (Comp)'); ?>
        <?php //MenuPermiss::ItemUrlPageSpan("rip_comp_interp_telep_12.php",'Telephone Report (Comp)'); ?>
        <?php //MenuPermiss::ItemUrlPageSpan("rip_comp_interp_trans_12.php",'Translation Report (Comp)'); ?>
        </ul>
        <?php MenuPermiss::ItemUrlPageSpan("rip_market.php",'Marketing Report'); ?>
            <?php } ?>



        </ul>
          </li>
              
             <?php MenuPermiss::ItemHdrUrlPage("rip_multiple_inv.php",'Invoices'); ?>
                <ul class="sub">

                <?php MenuPermiss::ItemUrlPageSpan("rip_multiple_inv.php",'Create Multiple Invoice'); ?>

             	<!--<li><a href="rip_multiple_inv_send.php">Multiple Invoice (Send)</a></li>
             	<li><a href="rip_multiple_inv_recev.php">Multiple Invoice (Received)</a></li>
             	<li><a href="rip_multiple_inv_paid.php">Multiple Invoice (Paid)</a></li>-->

                <?php MenuPermiss::ItemUrlPageSpan("multi_list_actions.php",'Multiple Invoice (Actions)'); ?>
                <?php MenuPermiss::ItemUrlPageSpan("interp_list_commit.php",'Interpreters Invoices Sent'); ?>
                <?php MenuPermiss::ItemUrlPageSpan("telep_list_commit.php",'Telephone Invoices Sent'); ?>
                <?php MenuPermiss::ItemUrlPageSpan("trans_list_commit.php",'Translation Invoices Sent'); ?>
                <?php MenuPermiss::ItemUrlPageSpan("interp_paid.php",'Paid  invoices (Intrp)'); ?>
                <?php MenuPermiss::ItemUrlPageSpan("telep_paid.php",'Paid  invoices (Telep)'); ?>
                <?php MenuPermiss::ItemUrlPageSpan("trans_paid.php",'Paid  invoices (Trans)'); ?>

                <?php MenuPermiss::ItemUrlOpen("invoice_query.php",'Invoices Registered',900,650); ?>
		
                <?php MenuPermiss::ItemUrlPageSpan("interp_pending_payment.php",'Pending  invoices (Intrp)'); ?>
                <?php MenuPermiss::ItemUrlPageSpan("telep_pending_payment.php",'Pending  invoices (Telep)'); ?>
                <?php MenuPermiss::ItemUrlPageSpan("trans_pending_payment.php",'Pending  invoices (Trans)'); ?>

               </ul>
          </li>
          
        <li><a href="#">Management</a>
        <ul class="sub">
            <?php 
            if(MenuPermiss::HasPriv('Management'))
            { 
                ?>
		<?php MenuPermiss::ItemUrlPageSpan("post_format.php",'Manage Posts'); ?>
		<?php MenuPermiss::ItemUrlPageSpan("email_invoice_body.php",'Email Formats'); ?>
		<?php MenuPermiss::ItemUrlPageSpan("manage_gallery.php",'Manage Gallery'); ?>
                <?php MenuPermiss::ItemUrlOpen("rates.php",'Manage Rates',900,650); ?>
                <?php MenuPermiss::ItemUrlOpen("comp_credit.php",'Update Credit',900,650); ?>

                <?php MenuPermiss::ItemUrlPageSpan("credit_list.php",'Credit List'); ?>
                <?php MenuPermiss::ItemUrlPageSpan("credit_list_full.php",'Credit List (Action)'); ?>
                <?php MenuPermiss::ItemUrlPageSpan("credit_list_full_trash.php",'Credit List <span style="color:#F00">Trash</span>'); ?>

                <?php MenuPermiss::ItemUrlOpen("bz_comp_credit.php",'Update Business',900,650); ?>

                <?php MenuPermiss::ItemUrlPageSpan("bz_credit_list.php",'Business List'); ?>
                <?php MenuPermiss::ItemUrlPageSpan("bz_credit_list_full.php",'Business List (Action)'); ?>
                <?php MenuPermiss::ItemUrlPageSpan("bz_credit_list_full_trash.php",'Business List <span style="color:#F00">Trash</span>'); ?>

            <?php 
            } 
        ?>

        <?php 
            if(MenuPermiss::HasPriv('Finance'))
            { 
                ?> 

                <?php MenuPermiss::ItemUrlOpen("rates.php",'Manage Rates',900,650); ?>
                <?php MenuPermiss::ItemUrlOpen("comp_credit.php",'Update Credit',900,650); ?>

                <?php MenuPermiss::ItemUrlPageSpan("credit_list.php",'Credit List'); ?>
                <?php MenuPermiss::ItemUrlPageSpan("credit_list_full.php",'Credit List (Action)'); ?>

            <?php 
            } 
        ?>

        <?php MenuPermiss::ItemUrlOpen("comp_type.php",'Company Type',900,650); ?>
        <?php MenuPermiss::ItemUrlOpen("exp_list_update.php",'Update Expenses List',900,650); ?>
        <?php MenuPermiss::ItemUrlOpen("cities.php",'Update Cities List',900,650); ?>
        <?php MenuPermiss::ItemUrlOpen("skills.php",'Manage Skills',900,650); ?>
        <?php MenuPermiss::ItemUrlOpen("language.php",'Manage Languages',900,650); ?>
        
        </ul> 

        <?php MenuPermiss::ItemHdrUrlPage("expense.php",'Expense'); ?>
            
        <ul class="sub">
  			
        <?php MenuPermiss::ItemUrlOpen("expense.php",'Comp Expenses',900,650); ?>

        <?php MenuPermiss::ItemUrlPageSpan("expence_list.php",'Expense List'); ?>
        <?php MenuPermiss::ItemUrlPageSpan("expence_list_trash.php",'Expense List <span style="color:#F00">Trash</span>'); ?>

        </ul>
        
        <?php MenuPermiss::ItemHdrUrlPage("rip_salary.php",'Salaries'); ?>
        <ul class="sub">

        <?php MenuPermiss::ItemUrlPageSpan("rip_salary.php",'Emp Salary'); ?>
        <?php MenuPermiss::ItemUrlPageSpan("emp_salary_list.php",'Emp Salary Details'); ?>
        <?php MenuPermiss::ItemUrlPageSpan("interp_work_list.php",'Interpreters Salary'); ?>
        
        </ul>
        <?php 
    } 
    ?>
        <?php MenuPermiss::ItemHdrUrlPage("interp_reg.php",'Registration'); ?>
        <ul class="sub">
        		
        <?php MenuPermiss::ItemUrlOpen("interp_reg.php",'Interpreter Registration',900,650); ?>
		
		<li><a href="reg_interp_list.php">Registered Interpreters</a></li> 
        <?php 
        if(MenuPermiss::HasPriv('Management'))
        {
            ?>
            <?php MenuPermiss::ItemUrlPageSpan("reg_interp_list_rest.php",'Registered Interpreters <span style="color:#F00">Trash</span> List'); ?>
            <?php 
        } ?>

        <?php MenuPermiss::ItemUrlOpen("comp_reg.php",'Company Registration',900,800); ?>

        <?php MenuPermiss::ItemUrlPageSpan("reg_comp_list.php",'Registered Companies'); ?>

        <?php 
            if(MenuPermiss::HasPriv('Management'))
            {
                ?>

                <?php MenuPermiss::ItemUrlPageSpan("reg_comp_list_rest.php",'Registered Companies <span style="color:#F00">Trash</span> List'); ?>

                <?php MenuPermiss::ItemUrlOpen("employee.php",'Emp Registration',900,700); ?>
        
                <?php 
            } ?>

            <?php MenuPermiss::ItemUrlPageSpan("emp_list.php",'Emp List'); ?>

            <?php 
            if(MenuPermiss::HasPriv('Management'))
            {
                ?>
                <?php MenuPermiss::ItemUrlPageSpan("emp_list_trash.php",'Emp <span style="color:#F00">Trash</span> List'); ?>
                <?php 
            } ?>

            </ul>
            </li>
            <?php 
            if(MenuPermiss::HasPriv('Management') || MenuPermiss::HasPriv('Finance'))
            {
                ?>  
                <?php MenuPermiss::ItemHdrUrlPage("interp_list_waist.php",'Other'); ?>
        
                <ul class="sub">

                <?php MenuPermiss::ItemUrlPageSpan("interp_list_waist.php",'Garbage-F2F List'); ?>
                <?php MenuPermiss::ItemUrlPageSpan("telep_list_waist.php",'Garbage-Telep List'); ?>
                <?php MenuPermiss::ItemUrlPageSpan("trans_list_waist.php",'Garbage-Trans List'); ?>
                <?php MenuPermiss::ItemUrlPageSpan("duedate_interp.php",'Due Date (Inpterp)'); ?>
                <?php MenuPermiss::ItemUrlPageSpan("duedate_telep.php",'Due Date (Telep)'); ?>
                <?php MenuPermiss::ItemUrlPageSpan("duedate_trans.php",'Due Date (Trans)'); ?>
                <?php MenuPermiss::ItemUrlPageSpan("advance_search.php",'Advance Search'); ?>
        
                <?php MenuPermiss::ItemUrlOpen("comp_query.php","Customer's History",900,650); ?>
                <?php MenuPermiss::ItemUrlOpen("vat_query.php",'VAT Collected',900,650); ?>
       
                </ul>
                </li>
                <?php 
            } 
            ?>

            <?php 
            if(MenuPermiss::HasPriv('Operator'))
            {
                ?>
                <?php MenuPermiss::ItemHdrUrlPage("language.php",'Management'); ?>

                <ul class="sub">
                <?php MenuPermiss::ItemUrlOpen("language.php",'Manage Languages',900,650); ?>
                <?php MenuPermiss::ItemUrlOpen("cities.php",'Update Cities List',900,650); ?>
                </ul> 
                <?php 
            } ?>

            <?php MenuPermiss::ItemHdrUrlPage("signup.php",'Admin'); ?>
            <ul class="sub">

                <?php 
                if(MenuPermiss::HasPriv('Management'))
                { 
                    ?>
                    <?php MenuPermiss::ItemUrlOpen("signup.php",'Sign Up',900,650); ?>
                    <?php MenuPermiss::ItemUrlPageSpan("reg_users_list.php",'Users List'); ?>
                    <?php 
                } ?>

                <?php MenuPermiss::ItemUrlOpen("change_pass.php",'Change Password',900,650); ?>
                <?php MenuPermiss::ItemUrlOpen("rolespermissions.php",'Roles Permissions',900,650); ?>
			
                </ul>
                <style>.logout:hover{cursor:pointer;}</style>
                <li><a class="logout" onclick="document.getElementById('id01').style.display='block'"><img src="images/arrow.gif" style="margin-left:-15px">Sign out</a></li>

            </li>
        </ul>
    </li>
    
</ul>
	</div>	

<footer>
			<hr />
	<p><strong>A Product of LSUK, Copyright © 2019-20</strong><hr/><div style="margin-left:19px;"></div></p>
		</footer>

</aside>
    <style>
#menu-v li, #menu-v a {zoom:1; } /* Hacks for IE6, IE7 */
#menu-v, #menu-v ul
{
    width: 265px; /* Main Menu Item widths */
    border: 1px solid #ccc;
    border-top:none;
    position: relative; font-size:0;
    list-style: none; margin: 0; padding: 0; display:block;
    z-index:9;
}
                
/* Top level menu links style
---------------------------------------*/

#menu-v li
{
    background: #FFF url(images/bg.gif) repeat-x 0 2px;
    list-style: none; margin: 0; padding: 0;
	height:25px;
}
#menu-v li a
{
    font: normal 12px Arial;
    border-top: 1px solid #ccc;
    display: block;
    /*overflow: auto; force hasLayout in IE7 */
    color: black;
    text-decoration: none;
    line-height:26px;
    padding-left:26px;            
}
#menu-v ul li a
{
    line-height:30px;
}
                
#menu-v li a.arrow:hover
{
    background-image:url(images/arrowon.gif);
    background-repeat: no-repeat;
    background-position: 97% 50%;
}
        
/*Sub level menu items
---------------------------------------*/
#menu-v li ul
{
    position: absolute;
    width: 200px; /*Sub Menu Items width */
    visibility:hidden;
}
        
#menu-v a.arrow
{
    background-image:url(images/arrow.gif);
    background-repeat: no-repeat;
    background-position: 97% 50%;
}
#menu-v li:hover, #menu-v li.onhover
{
    background-position:0 -62px;
}
#menu-v ul li
{
    background: rgba(255, 255, 255, 0.86);
    background-image:none;
}
#menu-v ul li:hover, #menu-v ul li.onhover
{
    background: #FFF;
    background-image:none;
}
        
/* Holly Hack for IE \
* html #menu-v  li
{
    float:left;
    height: 1%;
}
* html #menu-v  li a
{
    height: 1%;
}*/
/* End */</style>
<script>
var mcVM_options={menuId:"menu-v",alignWithMainMenu:true};
/* www.menucool.com/vertical/vertical-menu.*/
init_v_menu(mcVM_options);
function init_v_menu(a)
{
    if(window.addEventListener)window.addEventListener("load",function(){start_v_menu(a)},false);
    else 
        window.attachEvent&&window.attachEvent("onload",function(){start_v_menu(a)})
    }
function start_v_menu(i)
{
    var e=document.getElementById(i.menuId),j=e.offsetHeight,b=e.getElementsByTagName("ul"),g=/msie|MSIE 6/.test(navigator.userAgent);
    if(g)for(var h=e.getElementsByTagName("li"),a=0,l=h.length;a<l;a++){h[a].onmouseover=function(){this.className="onhover"};
    h[a].onmouseout=function(){this.className=""}}
    for(var k=function(a,b)
    {
        if(a.id==i.menuId)return b;
        else{b+=a.offsetTop;
            return k(a.parentNode.parentNode,b)}},a=0;a<b.length;a++)
            {var c=b[a].parentNode;c.getElementsByTagName("a")[0].className+=" arrow";
            b[a].style.left=c.offsetWidth+"px";b[a].style.top=c.offsetTop+"px";
            if(i.alignWithMainMenu){var d=k(c.parentNode,0);if(b[a].offsetTop+b[a].offsetHeight+d>j)
            {
                var f;if(b[a].offsetHeight>j)f=-d;else f=j-b[a].offsetHeight-d;b[a].style.top=f+"px"}}c.onmouseover=function()
                {
                    if(g)this.className="onhover";var a=this.getElementsByTagName("ul")[0];
                    if(a){a.style.visibility="visible";a.style.display="block"}};
                    c.onmouseout=function(){
                        if(g)this.className="";
                        this.getElementsByTagName("ul")[0].style.visibility="hidden";
                        this.getElementsByTagName("ul")[0].style.display="none"}}
                        for(var a=b.length-1;a>-1;a--)
                        b[a].style.display="none"}
    </script>

  <!-- Modal to display record -->
<link rel="stylesheet" href="css/w3.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <div id="id01" class="w3-modal">
 <div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
  <header class="w3-container w3-blue"> 
   <span onclick="document.getElementById('id01').style.display='none'" 
   class="w3-button w3-blue  w3-display-topright">&times;</span>
   <h4>Logout From LSUK</h4>
  </header>

 

  <div class="w3-container">
   <center><h4>Are you sure to logout?</h4>
            <a href="logout.php"><button class="w3-button w3-blue">Log out<i class="w3-margin-left fa fa-lock"></i></button></a></center><br>
  </div>


  <div class="w3-container w3-light-grey w3-padding">
   <button class="w3-button w3-right w3-white w3-border" 
   onclick="document.getElementById('id01').style.display='none'">Close</button>
  </div>
 </div>
</div>
<!--End of modal-->