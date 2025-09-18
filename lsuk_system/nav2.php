<style>
  .navbar-primary {
    background: #337ab7;
  }

  .navbar-primary a {
    color: #ffffff;
  }

  .navbar-primary a:hover,
  a:active {
    color: #000;
  }

  .open a {
    color: black;
  }

  .navbar-primary .navbar-toggle .icon-bar {
    background-color: #ffffff;
  }

  .navbar-toggle {
    border: 1px solid white;
  }

  .navbar-primary .dropdown-menu>li>a {
    padding: 6px 19px;
    border-bottom: 1px solid #b5afaf;
  }

  /* To Dropdown navbar dropdown on hover */
  /*.navbar-nav > li:hover > .dropdown-menu {*/
  /*    display: block;*/
  /*}*/
  .dropdown-submenu {
    position: relative;
  }

  .dropdown-submenu>.dropdown-menu {
    top: 0;
    left: 100%;
    margin-top: -2px;
    margin-left: -1px;
    -webkit-border-radius: 0 6px 6px 6px;
    -moz-border-radius: 0 6px 6px;
    border-radius: 0 6px 6px 6px;
  }

  .dropdown-submenu:hover>.dropdown-menu {
    display: block;
  }

  .navbar-primary .dropdown-menu>li>a {
    font-size: 16px;
  }

  .navbar-primary .dropdown-menu>li>a:focus,
  .navbar-primary .dropdown-menu>li>a:hover {
    color: #000000;
    background-color: #b9c1c5;
  }

  ::-webkit-scrollbar {
    width: 14px;
  }

  ::-webkit-scrollbar-thumb {
    background: #337ab7;
    border: 1px solid #fff;
  }

  ::-webkit-scrollbar-track {
    box-shadow: inset 0 0 3px grey;
    background: #ffffff61;
  }
  
  .btn-primary.active, .btn-primary:active, .open>.dropdown-toggle.btn-primary {
    background-color: #286090 !important;
}
li.dropdown-submenu a span
	{
		float: right;
	}
  .dropdown-menu {
    min-width: 250px;
  }
</style>
<?php include "permissmenuitem.php";
$not_allowed_op = [68];
$curPageName = substr($_SERVER["SCRIPT_NAME"], strrpos($_SERVER["SCRIPT_NAME"], "/") + 1);
$is_allowed_page_for_new_label = ($curPageName == "bk.php" || $curPageName == "reg_interp_list.php" || $curPageName == "home.php" || $curPageName == "tele_index.php" || $curPageName == "trans_index.php") && $_SESSION['Temp'] == 0 ? true : false;
if ($is_allowed_page_for_new_label) {
  $arr_is_temp = array();
  $count_is_temp = 0;
  $q_is_temp = $acttObj->read_specific("*", "(SELECT count(is_temp) as interpreters FROM `interpreter_reg` WHERE interpreter_reg.is_temp=1 and interpreter_reg.deleted_flag=0) as interpreters, (SELECT count(is_temp) as companies FROM comp_reg WHERE comp_reg.is_temp=1 and comp_reg.deleted_flag=0) as companies, (SELECT count(is_temp) as int_jobs FROM interpreter WHERE is_temp=1 and deleted_flag=0 and order_cancel_flag=0 and intrp_salary_comit=0) as int_jobs, (SELECT count(is_temp) as tp_jobs FROM telephone WHERE is_temp=1 and deleted_flag=0 and order_cancel_flag=0 and intrp_salary_comit=0) as tp_jobs, (SELECT count(is_temp) as tr_jobs FROM translation WHERE is_temp=1 and deleted_flag=0 and order_cancel_flag=0 and intrp_salary_comit=0) as tr_jobs", "1");
  if ($q_is_temp['interpreters'] != 0) {
    $count_is_temp = 1;
    array_push($arr_is_temp, '<b>' . sprintf("%02d", $q_is_temp['interpreters']) . '</b> Interpreter(s) registered');
  }
  if ($q_is_temp['companies'] != 0) {
    $count_is_temp = 1;
    array_push($arr_is_temp, '<b>' . sprintf("%02d", $q_is_temp['companies']) . '</b> company(s) registered');
  }
  if ($q_is_temp['int_jobs'] != 0) {
    $count_is_temp = 1;
    array_push($arr_is_temp, '<b>' . sprintf("%02d", $q_is_temp['int_jobs']) . '</b> Face To Face job(s) processed');
  }
  if ($q_is_temp['tp_jobs'] != 0) {
    $count_is_temp = 1;
    array_push($arr_is_temp, '<b>' . sprintf("%02d", $q_is_temp['tp_jobs']) . '</b> Telephone job(s) processed');
  }
  if ($q_is_temp['tr_jobs'] != 0) {
    $count_is_temp = 1;
    array_push($arr_is_temp, '<b>' . sprintf("%02d", $q_is_temp['tr_jobs']) . '</b> Translation job(s) processed');
  }
  //Call for update routes if any of route is allocated/removed
  if ($_SESSION['is_root'] == 0) {
    $get_routes = $acttObj->read_specific("GROUP_CONCAT(route_permissions.perm_id) as routes", "route_permissions,rolenamed", "route_permissions.role_id=rolenamed.id AND rolenamed.named='" . $_SESSION['prv'] . "'")['routes'];
    $get_allowed_routes = !empty($get_routes) ? explode(",", $get_routes) : array();
    if ($get_allowed_routes) {
      $_SESSION['allowed_routes'] = $get_allowed_routes;
    }
  }
  $get_amendment_requests_count = $acttObj->read_specific("count(*) as counter", "amendment_requests", "status=1")['counter'];
}
//Home routes
$route_home = $_SESSION['is_root'] == 1 || in_array(4, $_SESSION['allowed_routes']);
$route_tp = $_SESSION['is_root'] == 1 || in_array(5, $_SESSION['allowed_routes']);
$route_tr = $_SESSION['is_root'] == 1 || in_array(6, $_SESSION['allowed_routes']);
//HR routes
$route_new_interpreter = $_SESSION['is_root'] == 1 || in_array(27, $_SESSION['allowed_routes']);
$route_list_interpreters = $_SESSION['is_root'] == 1 || in_array(28, $_SESSION['allowed_routes']);
$route_new_company = $_SESSION['is_root'] == 1 || in_array(29, $_SESSION['allowed_routes']);
$route_list_company = $_SESSION['is_root'] == 1 || in_array(30, $_SESSION['allowed_routes']);
$route_new_emp = $_SESSION['is_root'] == 1 || in_array(203, $_SESSION['allowed_routes']);
$route_list_emp = $_SESSION['is_root'] == 1 || in_array(49, $_SESSION['allowed_routes']);
//FINANCE routes
$route_finance_manage_company_emails = $_SESSION['is_root'] == 1 || in_array(161, $_SESSION['allowed_routes']);
$route_finance_update_business = $_SESSION['is_root'] == 1 || in_array(45, $_SESSION['allowed_routes']);
$route_finance_list_business = $_SESSION['is_root'] == 1 || in_array(181, $_SESSION['allowed_routes']);
$route_finance_list_business_actions = $_SESSION['is_root'] == 1 || in_array(46, $_SESSION['allowed_routes']);
$route_finance_list_business_actions_trash = $_SESSION['is_root'] == 1 || in_array(182, $_SESSION['allowed_routes']);
$route_finance_customers_history = $_SESSION['is_root'] == 1 || in_array(206, $_SESSION['allowed_routes']);
$route_finance_vat_collected = $_SESSION['is_root'] == 1 || in_array(207, $_SESSION['allowed_routes']);
$route_finance_create_multiple_invocie = $_SESSION['is_root'] == 1 || in_array(15, $_SESSION['allowed_routes']);
$route_finance_multiple_invocies_actions = $_SESSION['is_root'] == 1 || in_array(16, $_SESSION['allowed_routes']);
$route_finance_po_add = $_SESSION['is_root'] == 1 || in_array(20, $_SESSION['allowed_routes']);
$route_finance_po_list = $_SESSION['is_root'] == 1 || in_array(44, $_SESSION['allowed_routes']);
$route_finance_po_requested = $_SESSION['is_root'] == 1 || in_array(155, $_SESSION['allowed_routes']);
$route_finance_po_trashed = $_SESSION['is_root'] == 1 || in_array(180, $_SESSION['allowed_routes']);
$route_finance_pending_all = $_SESSION['is_root'] == 1 || in_array(132, $_SESSION['allowed_routes']);
$route_finance_disposed_all = $_SESSION['is_root'] == 1 || in_array(188, $_SESSION['allowed_routes']);
$route_finance_paid_all = $_SESSION['is_root'] == 1 || in_array(133, $_SESSION['allowed_routes']);
$route_finance_invoices_registered = $_SESSION['is_root'] == 1 || in_array(183, $_SESSION['allowed_routes']);
$route_finance_add_expense = $_SESSION['is_root'] == 1 || in_array(23, $_SESSION['allowed_routes']);
$route_finance_list_expense = $_SESSION['is_root'] == 1 || in_array(24, $_SESSION['allowed_routes']);
$route_finance_add_supplier = $_SESSION['is_root'] == 1 || in_array(176, $_SESSION['allowed_routes']);
$route_finance_list_supplier = $_SESSION['is_root'] == 1 || in_array(175, $_SESSION['allowed_routes']);
$route_finance_add_receivable = $_SESSION['is_root'] == 1 || in_array(153, $_SESSION['allowed_routes']);
$route_finance_list_receivables = $_SESSION['is_root'] == 1 || in_array(152, $_SESSION['allowed_routes']);
$route_finance_list_request_types = $_SESSION['is_root'] == 1 || in_array(209, $_SESSION['allowed_routes']);
$route_finance_list_advances_list = $_SESSION['is_root'] == 1 || in_array(210, $_SESSION['allowed_routes']);
$route_finance_list_deductions_list = $_SESSION['is_root'] == 1 || in_array(211, $_SESSION['allowed_routes']);
$route_cancellation_report = $_SESSION['is_root'] == 1 || in_array(166, $_SESSION['allowed_routes']);
$route_pending_all = $_SESSION['is_root'] == 1 || in_array(12, $_SESSION['allowed_routes']);
$route_int_salary = $_SESSION['is_root'] == 1 || in_array(117, $_SESSION['allowed_routes']);
$route_int_paid_salary = $_SESSION['is_root'] == 1 || in_array(118, $_SESSION['allowed_routes']);
$route_lang_wise_hours = $_SESSION['is_root'] == 1 || in_array(163, $_SESSION['allowed_routes']);
$route_cons_quarter_units = $_SESSION['is_root'] == 1 || in_array(189, $_SESSION['allowed_routes']);
$route_cons_quarter_langs = $_SESSION['is_root'] == 1 || in_array(129, $_SESSION['allowed_routes']);
$route_cons_quarter_report = $_SESSION['is_root'] == 1 || in_array(128, $_SESSION['allowed_routes']);
$route_cons_comp_wise_all = $_SESSION['is_root'] == 1 || in_array(31, $_SESSION['allowed_routes']);
$route_cons_comp_wise = $_SESSION['is_root'] == 1 || in_array(32, $_SESSION['allowed_routes']);
$route_cons_monthly_all = $_SESSION['is_root'] == 1 || in_array(35, $_SESSION['allowed_routes']);
$route_cons_order_cancel_monthly = $_SESSION['is_root'] == 1 || in_array(38, $_SESSION['allowed_routes']);
$route_cons_order_cancel_monthly_tp = $_SESSION['is_root'] == 1 || in_array(39, $_SESSION['allowed_routes']);
$route_cons_order_cancel_monthly_tr = $_SESSION['is_root'] == 1 || in_array(40, $_SESSION['allowed_routes']);
$route_cons_overall_report_comp = $_SESSION['is_root'] == 1 || in_array(197, $_SESSION['allowed_routes']);
$route_cons_marketing_report = $_SESSION['is_root'] == 1 || in_array(113, $_SESSION['allowed_routes']);
$route_paid_all = $_SESSION['is_root'] == 1 || in_array(13, $_SESSION['allowed_routes']);
$route_po_report = $_SESSION['is_root'] == 1 || in_array(43, $_SESSION['allowed_routes']);
$route_business_report = $_SESSION['is_root'] == 1 || in_array(198, $_SESSION['allowed_routes']);
$route_summary_report = $_SESSION['is_root'] == 1 || in_array(14, $_SESSION['allowed_routes']);
$route_deletion_report = $_SESSION['is_root'] == 1 || in_array(213, $_SESSION['allowed_routes']);
$route_shifted_jobs = $_SESSION['is_root'] == 1 || in_array(214, $_SESSION['allowed_routes']);
$route_daily_allocated = $_SESSION['is_root'] == 1 || in_array(10, $_SESSION['allowed_routes']);
$route_daily_unallocated = $_SESSION['is_root'] == 1 || in_array(11, $_SESSION['allowed_routes']);
$route_vhs_daily = $_SESSION['is_root'] == 1 || in_array(199, $_SESSION['allowed_routes']);
$route_comp_expenses_report = $_SESSION['is_root'] == 1 || in_array(41, $_SESSION['allowed_routes']);
$route_overall_vat_report = $_SESSION['is_root'] == 1 || in_array(200, $_SESSION['allowed_routes']);
$route_job_list_wise_comp = $_SESSION['is_root'] == 1 || in_array(201, $_SESSION['allowed_routes']);
$route_int_lang_postal_code = $_SESSION['is_root'] == 1 || in_array(202, $_SESSION['allowed_routes']);
//BOOKING routes
$route_f2f_order = $_SESSION['is_root'] == 1 || in_array(1, $_SESSION['allowed_routes']);
$route_tp_order = $_SESSION['is_root'] == 1 || in_array(2, $_SESSION['allowed_routes']);
$route_tr_order = $_SESSION['is_root'] == 1 || in_array(3, $_SESSION['allowed_routes']);
$route_bk = $_SESSION['is_root'] == 1 || in_array(135, $_SESSION['allowed_routes']);
//PAYROLL routes
$route_payroll_report_emp_salary = $_SESSION['is_root'] == 1 || in_array(26, $_SESSION['allowed_routes']);
$route_payroll_emp_salary_details = $_SESSION['is_root'] == 1 || in_array(48, $_SESSION['allowed_routes']);
$route_payroll_int_salary = $_SESSION['is_root'] == 1 || in_array(25, $_SESSION['allowed_routes']);
$route_payroll_int_salary_report = $_SESSION['is_root'] == 1 || in_array(84, $_SESSION['allowed_routes']);
//MANAGEMENT routes
$route_mg_investigate_order = $_SESSION['is_root'] == 1 || in_array(208, $_SESSION['allowed_routes']);
$route_mg_complaints = $_SESSION['is_root'] == 1 || in_array(149, $_SESSION['allowed_routes']);
$route_mg_manage_porder = $_SESSION['is_root'] == 1 || in_array(131, $_SESSION['allowed_routes']);
$route_mg_rates_old = $_SESSION['is_root'] == 1 || in_array(184, $_SESSION['allowed_routes']);
$route_mg_cms = $_SESSION['is_root'] == 1 || in_array(127, $_SESSION['allowed_routes']);
$route_mg_manage_posts = $_SESSION['is_root'] == 1 || in_array(119, $_SESSION['allowed_routes']);
$route_mg_email_formats = $_SESSION['is_root'] == 1 || in_array(105, $_SESSION['allowed_routes']);
$route_mg_manage_gallery = $_SESSION['is_root'] == 1 || in_array(106, $_SESSION['allowed_routes']);
$route_mg_manage_global_rates = $_SESSION['is_root'] == 1 || in_array(168, $_SESSION['allowed_routes']);
$route_mg_manage_interpreter_rates = $_SESSION['is_root'] == 1 || in_array(171, $_SESSION['allowed_routes']);
$route_mg_comp_type = $_SESSION['is_root'] == 1 || in_array(47, $_SESSION['allowed_routes']);
$route_mg_exp_list_update = $_SESSION['is_root'] == 1 || in_array(26, $_SESSION['allowed_routes']);
$route_mg_skills = $_SESSION['is_root'] == 1 || in_array(114, $_SESSION['allowed_routes']);
$route_mg_cities = $_SESSION['is_root'] == 1 || in_array(21, $_SESSION['allowed_routes']);
$route_mg_languages = $_SESSION['is_root'] == 1 || in_array(22, $_SESSION['allowed_routes']);
$route_mg_daily_logs = $_SESSION['is_root'] == 1 || in_array(160, $_SESSION['allowed_routes']);
$route_mg_new_user = $_SESSION['is_root'] == 1 || in_array(185, $_SESSION['allowed_routes']);
$route_mg_users_list = $_SESSION['is_root'] == 1 || in_array(186, $_SESSION['allowed_routes']);
$route_mg_change_password = $_SESSION['is_root'] == 1 || in_array(51, $_SESSION['allowed_routes']);
$route_mg_roles_permissions = $_SESSION['is_root'] == 1 || in_array(167, $_SESSION['allowed_routes']);
$route_mg_survey = $_SESSION['is_root'] == 1 || in_array(187, $_SESSION['allowed_routes']);
$route_mg_events = $_SESSION['is_root'] == 1 || in_array(165, $_SESSION['allowed_routes']);
$route_mg_banks = $_SESSION['is_root'] == 1 || in_array(224, $_SESSION['allowed_routes']);

$route_company_invoice_add = $_SESSION['is_root'] == 1 || in_array(218, $_SESSION['allowed_routes']);
$route_company_invoice_list = $_SESSION['is_root'] == 1 || in_array(216, $_SESSION['allowed_routes']);
$route_company_invoice_del = $_SESSION['is_root'] == 1 || in_array(217, $_SESSION['allowed_routes']);
//EXTRAS routes
$route_amendment_requests = $_SESSION['is_root'] == 1 || in_array(212, $_SESSION['allowed_routes']);

if ($_SESSION['is_root'] == 0) {
  $get_page_access = $acttObj->read_specific("GROUP_CONCAT(action_id) as ids", "action_permissions", "user_id=" . $_SESSION['userId'])['ids'];
  $_SESSION['get_page_access'] = explode(",", $get_page_access);
 
}

$route_view_account_statements = $_SESSION['is_root'] == 1 || in_array(235, $_SESSION['get_page_access']);
$route_view_journal_ledger_bank = $_SESSION['is_root'] == 1 || in_array(236, $_SESSION['get_page_access']);
$route_view_journal_ledger_cash = $_SESSION['is_root'] == 1 || in_array(237, $_SESSION['get_page_access']);

$route_prepayments_list = $_SESSION['is_root'] == 1 || in_array(225, $_SESSION['allowed_routes']);

$route_add_prepayment = $_SESSION['is_root'] == 1 || in_array(243, $_SESSION['get_page_access']);
$route_edit_prepayment = $_SESSION['is_root'] == 1 || in_array(244, $_SESSION['get_page_access']);
$route_view_prepayment = $_SESSION['is_root'] == 1 || in_array(245, $_SESSION['get_page_access']);
$route_delete_prepayment = $_SESSION['is_root'] == 1 || in_array(246, $_SESSION['get_page_access']);
$route_restore_prepayment = $_SESSION['is_root'] == 1 || in_array(247, $_SESSION['get_page_access']);
$route_pay_prepayment = $_SESSION['is_root'] == 1 || in_array(248, $_SESSION['get_page_access']);

?>

<nav class="navbar navbar-primary">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="javascript:void(0)">
        <?php if (!empty($_SESSION['UserName'])) {
          echo ucwords(@$_SESSION['UserName']) . ' (' . $_SESSION['prv'] . ')';
        } else {
          echo '<script>window.location="index.php";</script>';
        } ?>
      </a>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav">
        <?php if ($is_allowed_page_for_new_label) {
          if ($count_is_temp == 1) { ?>
            <li title="Temporary Role operations" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-content="<?php print_r(implode('<br>', $arr_is_temp)); ?>"><a href="javascript:void(0)"><img src="images/new-label.png" width="30" style="margin:-7px -7px;" /></a></li>
          <?php }
        }
        if ($route_home || $route_tp || $route_tr) { ?>
          <li class="active"><a href="home.php"><b>HOME</b></a></li>
        <?php }
        if ( !in_array($_SESSION['userId'],$not_allowed_op) && ($route_new_interpreter || $route_list_interpreters || $route_new_company || $route_list_company || $route_new_emp || $route_list_emp)) { ?>
          <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="javascript:void(0)">HR <span class="caret"></span></a>
            <ul class="dropdown-menu">
              <?php if ($route_new_interpreter) { ?>
                <li><a href="javascript:void(0)" onClick="popupwindow('interp_reg.php', 'Register New Interpreter', 1100, 910);">Register <span class="label label-info">New</span> Interpreter</a></li>
              <?php }
              if ($route_list_interpreters) { ?>
                <li><a href="reg_interp_list.php">Registered Interpreters</a></li>
              <?php }
              if ($route_new_company) { ?>
                <li><a href="javascript:void(0)" onClick="popupwindow('comp_reg.php', 'Register New Company', 1100, 820);">Register <span class="label label-success">New</span> Company</a></li>
              <?php }
              if ($route_list_company) {
                MenuPermiss::ItemUrlPageSpan("reg_comp_list.php", 'Registered Companies');
              }
              if ($route_new_emp) { ?>
                <li><a href="javascript:void(0)" onClick="popupwindow('employee.php', 'Register New Employee', 1100, 570);">Register <span class="label label-warning">New</span> Employee</a></li>
              <?php }
              if ($route_list_emp) {
                MenuPermiss::ItemUrlPageSpan("emp_list.php", 'Employees List');
              } ?>
          </li>
                      <?php if ($route_finance_manage_company_emails) { ?>
              <li><a href="javascript:void(0)" onClick="popupwindow('manage_comp_mails.php', 'Manage Company Emails', 1200, 900);">Manage Company Emails</a></li>
            <?php } ?>
          </ul>
          </li>
        <?php } ?>

      <?php $tab_business = $route_finance_update_business || $route_finance_list_business || $route_finance_list_business_actions || $route_finance_list_business_actions_trash ? true : false;
      $tab_purchase_orders = $route_finance_po_add || $route_finance_po_list || $route_finance_po_requested || $route_finance_po_trashed ? true : false;
      $tab_company_income_invoice = $route_company_invoice_del || $route_company_invoice_list || $route_company_invoice_add ? true : false;      
      $tab_receivables = $route_finance_add_receivable || $route_finance_list_receivables ? true : false;
      $tab_money_requests = $route_finance_list_request_types || $route_finance_list_advances_list || $route_finance_list_deductions_list ? true : false;
      $tab_expenses = $route_finance_add_expense || $route_finance_add_supplier || $route_finance_list_expense || $route_finance_list_supplier ? true : false;
      $tab_invoices = $route_finance_create_multiple_invocie || $route_finance_multiple_invocies_actions || $tab_purchase_orders || $route_finance_pending_all || $route_finance_disposed_all || $route_finance_paid_all || $route_finance_invoices_registered ? true : false;
      $tab_accounts_statements = $route_view_account_statements || $route_view_journal_ledger_bank || $route_view_journal_ledger_cash ? true : false;
      $tab_pre_payments = $route_prepayments_list || $route_add_prepayment || $route_edit_prepayment || $route_view_prepayment || $route_delete_prepayment || $route_restore_prepayment || $route_pay_prepayment ? true : false;
      
      if ($tab_business || $tab_purchase_orders || $tab_receivables || $tab_money_requests || $tab_expenses || $tab_invoices || $route_finance_manage_company_emails || $route_finance_customers_history || $route_finance_vat_collected || $tab_accounts_statements || $tab_pre_payments) { ?>
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">FINANCE <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <?php if ($route_finance_manage_company_emails) { 
              // <li><a href="javascript:void(0)" onClick="popupwindow('manage_comp_mails.php', 'Manage Company Emails', 1200, 900);">Manage Company Emails</a></li>
             }
            if ($tab_business || $route_finance_customers_history || $route_finance_vat_collected) { ?>
              <li class="dropdown-submenu">
                <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><b class="text-success">Revenue</b> <span class="glyphicon glyphicon-arrow-right"></span></a>
                <ul class="dropdown-menu">
                  <?php if ($tab_business) { ?>
                    <li class="dropdown-submenu">
                      <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">Business <span class="glyphicon glyphicon-arrow-right"></span></a>
                      <ul class="dropdown-menu">
                        <?php if ($route_finance_update_business) { ?>
                          <li><a href="javascript:void(0)" onClick="popupwindow('bz_comp_credit.php','Business Credit List',850,500)">Update Business</a></li>
                        <?php }
                        if ($route_finance_list_business) {
                          MenuPermiss::ItemUrlPageSpan("bz_credit_list.php", 'Business List'); ?>
                        <?php }
                        if ($route_finance_list_business_actions) {
                          MenuPermiss::ItemUrlPageSpan("bz_credit_list_full.php", 'Business List (Action)'); ?>
                        <?php }
                        if ($route_finance_list_business_actions_trash) {
                          MenuPermiss::ItemUrlPageSpan("bz_credit_list_full_trash.php", 'Business List <span style="color:#F00">Trash</span>');
                        } ?>
                      </ul>
                    </li>
                  <?php }
                  if ($route_finance_customers_history) {
                    MenuPermiss::ItemUrlOpen("comp_query.php", "Customer's History", 900, 650);
                  }
                  if ($route_finance_vat_collected) {
                    MenuPermiss::ItemUrlOpen("vat_query.php", 'VAT Collected', 900, 650);
                  } ?>
                </ul>
              </li>
            <?php }
            if ($tab_invoices) { ?>
              <li class="dropdown-submenu">
                <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">Invoices <span class="glyphicon glyphicon-arrow-right"></span></a>
                <ul class="dropdown-menu">
                  <?php
                  if ($tab_company_income_invoice) { ?>
                    <li class="dropdown-submenu">
                      <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">Manual Invoice <span class="glyphicon glyphicon-arrow-right"></span></a>
                      <ul class="dropdown-menu">
                        <?php if ($route_company_invoice_add) { ?>
                          <li><a href="javascript:void(0)" onClick="popupwindow('create_manual_invoice.php','Add New Invoice',950,700)">Add New Invoice</a></li>
                        <?php }
                        if ($route_company_invoice_list) { ?>
                        <li>
                          <a href="manual_invoice.php">Invoice List</a>
                        </li>
                      <?php }
                        ?>
                      </ul>
                    </li>
                  <?php } ?>
                  <?php if ($route_finance_create_multiple_invocie) { ?>
                  <li class="dropdown-submenu">
                    <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">Collective Invoices <span class="glyphicon glyphicon-arrow-right"></span></a>
                    <ul class="dropdown-menu">
                      <?php if ($route_finance_create_multiple_invocie) { ?>
                        <li>
                          <a href="rip_multiple_inv.php" title="New Collective Invoice">Add New Invoice</a>
                        </li>
                      <?php } ?>
                      <?php if ($route_finance_multiple_invocies_actions) { ?>
                        <li>
                          <a href="multi_list_actions.php" title="Collective Invoices List">Invoice List</a>
                        </li>
                      <?php } ?>
                    </ul>
                  </li>
                <?php } ?>

                <?php if ($tab_purchase_orders) { ?>
                    <li class="dropdown-submenu">
                      <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">Purchase Orders <span class="glyphicon glyphicon-arrow-right"></span></a>
                      <ul class="dropdown-menu">
                        <?php if ($route_finance_po_add) { ?>
                          <li><a href="javascript:void(0)" onClick="popupwindow('comp_credit.php','Company Credit List',950,700)">Add New PO</a></li>
                        <?php }
                        if ($route_finance_po_list) {
                          MenuPermiss::ItemUrlPageSpan("credit_list_full.php", 'PO List');
                        }
                        if ($route_finance_po_requested) { ?>
                          <li class="bg-warning"><a href="po_requested.php">PO Requested</a></li>
                        <?php }
                        if ($route_finance_po_trashed) {
                          MenuPermiss::ItemUrlPageSpan("credit_list_full_trash.php", '<span style="color:#F00">Trashed PO</span>');
                        } ?>
                      </ul>
                    </li>
                  <?php }
                  if ($route_finance_pending_all) { ?>
                    <li class="bg-warning"><a href="all_cn_payment.php">Credit Notes (All)</a></li>
                  <?php }
                  if ($route_finance_pending_all) { ?>
                    <li class="bg-danger"><a href="all_pending_payment.php">Pending Invoices (All)</a></li>
                  <?php }
                  if ($route_finance_disposed_all) { ?>
                    <li class="bg-danger"><a href="all_disposed_payment.php">Disposed of Invoices</a></li>
                  <?php }
                  if ($route_finance_paid_all) { ?>
                    <li class="bg-success"><a href="all_paid_payment.php">Paid Invoices (All)</a></li>
                  <?php }
                  if ($route_finance_invoices_registered) {
                    MenuPermiss::ItemUrlOpen("invoice_query.php", 'Invoices Registered', 900, 650);
                  } ?>
                </ul>
              </li>
            <?php }
            if ($tab_expenses) { ?>
              <li class="dropdown-submenu">
                <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">Expenses <span class="glyphicon glyphicon-arrow-right"></span></a>
                <ul class="dropdown-menu">
                  <?php if ($route_finance_add_expense) { ?>
                    <li><a href="javascript:void(0)" onClick="popupwindow('expense.php?act=new', 'Create New Expense', 1000,700);"><i class="glyphicon glyphicon-plus"></i> Company Expense</a></li>
                  <?php }
                  if ($route_finance_add_supplier) { ?>
                    <li><a href="javascript:void(0)" onClick="popupwindow('sup_reg.php', 'Create New Supplier', 800,500);"><i class="glyphicon glyphicon-plus"></i> Add Supplier</a></li>
                  <?php }
                  if ($route_finance_list_expense) { ?>
                    <li><a href="expence_list.php"><i class="glyphicon glyphicon-list"></i> Expense List</a></li>
                  <?php }
                  if ($route_finance_list_supplier) { ?>
                    <li><a href="reg_sup_list.php"><i class="glyphicon glyphicon-list"></i> Supplier List</a></li>
                  <?php } ?>
                </ul>
              </li>
            <?php }
            if ($tab_receivables) { ?>
              <li class="dropdown-submenu">
                <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">Receivable <span class="glyphicon glyphicon-arrow-right"></span></a>
                <ul class="dropdown-menu">
                  <?php if ($route_finance_add_receivable) { ?>
                    <li><a href="javascript:void(0)" onClick="popupwindow('receivable.php', 'Create New Receivable', 1200,800);"><i class="glyphicon glyphicon-plus"></i> Add Receivable</a></li>
                  <?php }
                  if ($route_finance_list_receivables) { ?>
                    <li><a href="receivable_list.php"><i class="glyphicon glyphicon-list"></i> Receivable List</a></li>
                  <?php } ?>
                </ul>
              </li>
            <?php } ?>
            <?php if($tab_pre_payments){ ?>
              <li class="dropdown-submenu">
                  <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">Prepayments <span class="glyphicon glyphicon-arrow-right"></span></a>
                  <ul class="dropdown-menu">
                    <?php if ($route_add_prepayment) { ?>
                      <li><a href="javascript:void(0)" onClick="popupwindow('pre_payments.php?act=new', 'Add New Prepayment', 1200,800);"><i class="glyphicon glyphicon-plus"></i> Add Prepayment</a></li>
                    <?php }
                    if ($route_prepayments_list) { ?>
                      <li><a href="pre_payment_list.php"><i class="glyphicon glyphicon-list"></i> Prepayments List</a></li>
                    <?php } ?>
                  </ul>
                </li>
            <?php } ?>
            <?php if ($tab_money_requests) { ?>
              <li class="dropdown-submenu">
                <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">Advance & Deductions <span class="glyphicon glyphicon-arrow-right"></span></a>
                <ul class="dropdown-menu">
                  <?php if ($route_finance_list_request_types) { ?>
                    <li><a href="javascript:void(0)" onClick="popupwindow('request_types.php', 'View Request Types', 1200,800);"><i class="glyphicon glyphicon-list"></i> Request Types</a></li>
                  <?php }
                  if ($route_finance_list_advances_list) { ?>
                    <li class="bg-success"><a href="money_requests_a.php"><i class="glyphicon glyphicon-plus"></i> Advances List</a></li>
                  <?php }
                  if ($route_finance_list_deductions_list) { ?>
                    <li class="bg-danger"><a href="money_requests_d.php"><i class="glyphicon glyphicon-remove"></i> Deductions List</a></li>
                  <?php } ?>
                </ul>
              </li>
            <?php } ?>

            <?php if ($tab_accounts_statements) { ?>
            <li class="dropdown-submenu">
              <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">Accounts <span class="glyphicon glyphicon-arrow-right"></span></a>
              <ul class="dropdown-menu">
                <?php if ($route_view_account_statements) { ?>
                  <li>
                    <a href="account_income_statement.php">
                      <i class="fa fa-line-chart" aria-hidden="true"></i> Income Statement
                    </a>
                  </li>
                  <li>
                    <a href="account_receivable_statement.php">
                      <i class="fa fa-area-chart" aria-hidden="true"></i> Receivable Statement
                    </a>
                  </li>
                  <li>
                    <a href="account_expenses_statement.php">
                      <i class="fa fa-line-chart" aria-hidden="true"></i> Expenses Statement
                    </a>
                  </li>
                  <li>
                    <a href="account_payables_statement.php">
                      <i class="fa fa-area-chart" aria-hidden="true"></i> Payables Statement
                    </a>
                  </li>
                <?php } ?>
                <?php if ($route_view_journal_ledger_bank || $route_view_journal_ledger_cash) { ?>
                  <li class="dropdown-submenu">
                    <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">
                      <i class="fa fa-bar-chart" aria-hidden="true"></i> Journal Ledger <span class="glyphicon glyphicon-arrow-right"></span>
                    </a>
                    <ul class="dropdown-menu">
                      <?php if ($route_view_journal_ledger_bank) { ?>
                        <li>
                          <a href="account_journal_ledger_bank.php">
                            <i class="fa fa-bank" aria-hidden="true"></i> Bank Statement
                          </a>
                        </li>
                      <?php } ?>
                      <?php if ($route_view_journal_ledger_cash) { ?>
                        <li>
                          <a href="account_journal_ledger_cash.php">
                            <i class="fa fa-money" aria-hidden="true"></i> Cash Statement
                          </a>
                        </li>
                      <?php } ?>
                    </ul>
                  </li>
                <?php } ?>
              </ul>
            </li>
          <?php } ?>

          </ul>
        </li>
      <?php }
      $tab_consolidate_reports = $route_cons_quarter_units || $route_cons_quarter_langs || $route_cons_quarter_report || $route_cons_comp_wise_all || $route_cons_comp_wise ||
        $route_cons_monthly_all || $route_cons_order_cancel_monthly || $route_cons_order_cancel_monthly_tp || $route_cons_order_cancel_monthly_tr || $route_cons_overall_report_comp || $route_cons_marketing_report ? true : false;
      if ( !in_array($_SESSION['userId'],$not_allowed_op) && ($route_cancellation_report || $route_pending_all || $route_int_salary || $route_int_paid_salary || $route_lang_wise_hours || $tab_consolidate_reports ||
        $route_paid_all || $route_po_report || $route_business_report || $route_summary_report || $route_deletion_report || $route_shifted_jobs|| $route_daily_allocated || $route_daily_unallocated || $route_vhs_daily ||
        $route_comp_expenses_report || $route_overall_vat_report || $route_job_list_wise_comp || $route_int_lang_postal_code)
      ) { ?>
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">REPORTS <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <?php if ($route_cancellation_report) { ?>
              <li class="bg-danger"><a href="rip_cancelled_jobs.php">Cancellation Report</a></li>
            <?php }
            if ($route_pending_all) {
              MenuPermiss::ItemUrlPageSpan("rip_pending_all.php", 'Pending  invoices (All)');
            }
            if ($route_int_salary) {
              MenuPermiss::ItemUrlPageSpan("rip_interpreters_salary.php", 'Interpreters Salary Report');
            }
            if ($route_int_paid_salary) {
              MenuPermiss::ItemUrlPageSpan("rip_interpreters_salary_paid.php", 'Interpreters Paid Salary Report');
            }
            if ($route_lang_wise_hours) {
              MenuPermiss::ItemUrlPageSpan("rip_lang_hr.php", 'Language wise hours report');
            }
            if ($tab_consolidate_reports) { ?>
              <li class="dropdown-submenu">
                <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><b class="text-danger">Consolidate Reports</b> <span class="glyphicon glyphicon-arrow-right"></span></a>
                <ul class="dropdown-menu">
                  <?php if ($route_cons_quarter_units) { ?>
                    <li class="bg-success"><a href="rip_con_qtr_units.php"><b>Consolidated Quarterly (Units)</b></a></li>
                  <?php }
                  if ($route_cons_quarter_langs) { ?>
                    <li class="bg-success"><a href="rip_consolidate_quarter.php"><b>Consolidated Quarterly (Lang)</b></a></li>
                  <?php }
                  if ($route_cons_quarter_report) {
                    MenuPermiss::ItemUrlPageSpan("rip_consolidate_monthly_new.php", 'Consolidated Quarterly Report');
                  }
                  if ($route_cons_comp_wise_all) {
                    MenuPermiss::ItemUrlPageSpan("rip_consolidate_monthly_all.php", 'Consolidated Comp-Wise(All)');
                  }
                  if ($route_cons_comp_wise) {
                    MenuPermiss::ItemUrlPageSpan("rip_consolidate.php", 'Consolidated Comp-Wise');
                  }
                  if ($route_cons_monthly_all) {
                    MenuPermiss::ItemUrlPageSpan("rip_consolidate_monthly.php", 'Consolidated Monthly(All)');
                  }
                  if ($route_cons_order_cancel_monthly) {
                    MenuPermiss::ItemUrlPageSpan("rip_consolidate_cancelled.php", 'Order Cancel Monthly');
                  }
                  if ($route_cons_order_cancel_monthly_tp) {
                    MenuPermiss::ItemUrlPageSpan("rip_consolidate_cancelled_telep.php", 'Order Cancel Monthly(telep)');
                  }
                  if ($route_cons_order_cancel_monthly_tr) {
                    MenuPermiss::ItemUrlPageSpan("rip_consolidate_cancelled_trans.php", 'Order Cancel Monthly(trans)');
                  }
                  if ($route_cons_overall_report_comp) {
                    MenuPermiss::ItemUrlPageSpan("rip_comp_interp_12.php", 'Overall Report (Comp)');
                  }
                  if ($route_cons_marketing_report) {
                    MenuPermiss::ItemUrlPageSpan("rip_market.php", 'Marketing Report');
                  } ?>
                </ul>
              </li>
            <?php }
            if ($route_paid_all) {
              MenuPermiss::ItemUrlPageSpan("rip_paid_all.php", 'Paid  invoices (All)');
            }
            if ($route_po_report) {
              MenuPermiss::ItemUrlPageSpan("rip_po.php", 'Purchase Order Report');
            }
            if ($route_business_report) {
              MenuPermiss::ItemUrlPageSpan("rip_bo.php", 'Business Order Report');
            }
            if ($route_summary_report) {
              MenuPermiss::ItemUrlPageSpan("rip_summary.php", 'Summary Report');
            }
            if ($route_deletion_report) {
              MenuPermiss::ItemUrlPageSpan("rip_deletion_report.php", 'Deleted Jobs Report (Home Screen)');
            }
            if ($route_shifted_jobs) {
              MenuPermiss::ItemUrlPageSpan("rip_shifted_jobs.php", 'Shifted Jobs Report');
            }
            if ($route_daily_allocated) {
              MenuPermiss::ItemUrlPageSpan("rip_daily.php", 'Daily Report (Allocated)');
            }
            if ($route_daily_unallocated) {
              MenuPermiss::ItemUrlPageSpan("rip_daily_unaloc.php", 'Daily Report (Un-Allocated)');
            }
            if ($route_vhs_daily) { ?>
              <li><a href="booking_report.php">VHS Daily Report</a></li>
            <?php }
            if ($route_comp_expenses_report) {
              MenuPermiss::ItemUrlPageSpan("rip_comp_exp.php", 'Company Expenses Report'); ?>
            <?php }
            if ($route_overall_vat_report) {
              MenuPermiss::ItemUrlPageSpan("rip_vat.php", 'Overall VAT Report');
            }
            if ($route_job_list_wise_comp || $route_int_lang_postal_code) { ?>
              <li class="dropdown-submenu">
                <a href="javascript::void(0)" class="dropdown-toggle" data-toggle="dropdown"><b class="text-danger">Graphical Report</b> <span class="glyphicon glyphicon-arrow-right"></span></a>
                <ul class="dropdown-menu">
                  <?php if ($route_job_list_wise_comp) { ?>
                    <li><a href="graph1.php">Job List Company wise</a></li>
                  <?php }
                  if ($route_int_lang_postal_code) { ?>
                    <li><a href="interp_graphicalReport.php">Interpreters & Languages Postal Code-wise </a></li>
                  <?php } ?>
                </ul>
              </li>
            <?php } ?>
        </li>
        <li>
          </ul>
        </li>
      <?php }
      if ($route_f2f_order || $route_tp_order || $route_tr_order || $route_bk) { ?>
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">BOOKING <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <?php if ($route_f2f_order) { ?>
              <li><a href="javascript:void(0)" onClick="popupwindow('interpreter.php', 'Create Face To Face Job', 1250, 730);"><i class="glyphicon glyphicon-user"></i> Face To Face Order</a></li>
            <?php }
            if ($route_tp_order) { ?>
              <li><a href="javascript:void(0)" onClick="popupwindow('telephone.php', 'Create Telephone Job', 1250, 730);"><i class="glyphicon glyphicon-phone"></i> Telephone Order</a></li>
            <?php }
            if ($route_tr_order) { ?>
              <li><a href="javascript:void(0)" onClick="popupwindow('translation.php', 'Create Translation Job', 1250, 730);"><i class="glyphicon glyphicon-globe"></i> Translation Order</a></li>
            <?php }
            if ($route_bk) { ?>
              <li><a href="bk.php"><span class="glyphicon glyphicon-list"></span> Booking Lists</a></li>
            <?php } ?>
          </ul>
        </li>
      <?php }
      if ($route_payroll_report_emp_salary || $route_payroll_emp_salary_details || $route_payroll_int_salary || $route_payroll_int_salary_report) { ?>
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="javascript:void(0)">PAYROLL <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <?php if ($route_payroll_report_emp_salary) {
              MenuPermiss::ItemUrlPageSpan("rip_salary.php", 'Report Emp Salary');
            }
            if ($route_payroll_emp_salary_details) {
              MenuPermiss::ItemUrlPageSpan("emp_salary_list.php", 'Emp Salary Details');
            }
            if ($route_payroll_int_salary) {
              MenuPermiss::ItemUrlPageSpan("interp_work_list.php", 'Interpreters Salary List');
            }
            if ($route_payroll_int_salary_report) {
              MenuPermiss::ItemUrlPageSpan("reg_interp_salary_list.php", 'Interpreters Salary Status');
            } 
            ?>
          </ul>
        </li>
      <?php } ?>
      <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="javascript:void(0)">MANAGEMENT <span class="caret"></span></a>
        <ul class="dropdown-menu">
          <?php if ($route_mg_investigate_order) { ?>
            <li><a href="javascript:void(0)" onClick="popupwindow('investigate_order.php', 'Investigate an Order', 900, 700);">Investigate an Order <span class="label label-warning">New</span></a></li>
          <?php }
          if ($route_mg_complaints) { ?>
            <li><a href="complaints.php?1&deleted=0">Complaints</a></li>
          <?php }
          if ($route_mg_manage_porder) { ?>
            <li><a href="javascript:void(0)" onClick="popupwindow('manage_porder.php', 'Manage Purchase Orders', 900, 700);">Manage Purchase Orders</a></li>
          <?php }
          if ($route_mg_rates_old) {
            MenuPermiss::ItemUrlOpen("rates.php", 'Manage Rates Old', 900, 650); ?>
          <?php }
          if ($route_mg_cms || $route_mg_manage_posts || $route_mg_email_formats || $route_mg_manage_gallery || $route_mg_banks) { ?>
            <li class="dropdown-submenu">
              <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">Manage Content <span class="glyphicon glyphicon-arrow-right"></span></a>
              <ul class="dropdown-menu">
                <?php if ($route_mg_cms) {
                  MenuPermiss::ItemUrlPageSpan("cms.php", 'LSUK CMS');
                }
                if ($route_mg_manage_posts) {
                  MenuPermiss::ItemUrlPageSpan("post_format.php", 'Manage Posts');
                }
                if ($route_mg_email_formats) {
                  MenuPermiss::ItemUrlPageSpan("email_invoice_body.php", 'Email Formats');
                }
                if ($route_mg_manage_gallery) {
                  MenuPermiss::ItemUrlPageSpan("manage_gallery.php", 'LSUK Gallery');
                }
                if ($route_mg_banks) {
                  MenuPermiss::ItemUrlOpen("manage_bank_list.php", 'Manage Banks', 1000, 700);
                } ?>
              </ul>
            </li>
          <?php }
          if ($route_mg_manage_global_rates) { ?>
            <li><a href="javascript:void(0)" onClick="popupwindow('manage_global_rates.php', 'Global Company Rates', 1100, 1000);"><b>Global Company Rates</b></a></li>
          <?php }
          if ($route_mg_manage_global_rates) { ?>
            <li><a href="javascript:void(0)" onClick="popupwindow('manage_interpreter_rates.php', 'Global Interpreter Rates', 1100, 1000);"><b>Global Interpreter Rates</b></a></li>
          <?php }
          if ($route_mg_comp_type) { ?>
            <li><a href="javascript:void(0)" onClick="popupwindow('comp_type.php', 'Manage Company Types', 1000, 800);">Company Types</a></li>
          <?php }
          if ($route_mg_exp_list_update) {
            MenuPermiss::ItemUrlOpen("exp_list_update.php", 'Expenses Categories', 900, 650);
          }
          if ($route_mg_skills) {
            MenuPermiss::ItemUrlOpen("skills.php", 'Manage Skills', 900, 650);
          }
          if ($route_mg_cities) {
            MenuPermiss::ItemUrlOpen("cities.php", 'Update Cities List', 900, 650);
          }
          if ($route_mg_languages) { ?>
            <li><a href="javascript:void(0)" onClick="popupwindow('language.php', 'Manage Languages', 1100, 850);">Manage Languages</a></li>
          <?php }
          if ($route_mg_daily_logs || $route_mg_new_user || $route_mg_users_list || $route_mg_change_password || $route_mg_roles_permissions) { ?>
            <li class="dropdown-submenu">
              <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">Admin <span class="glyphicon glyphicon-arrow-right"></span></a>
              <ul class="dropdown-menu">
                <?php if ($route_mg_daily_logs) { ?>
                  <li><a href="summary_logs.php">Daily Logs</a></li>
                <?php }
                if ($route_mg_new_user) { ?>
                  <li><a href="javascript:void(0)" onClick="popupwindow('signup.php', 'Add New LSUK User', 1100, 570);">Add New LSUK User</a></li>
                <?php }
                if ($route_mg_users_list) { ?>
                  <li><a href="reg_users_list.php?1&user_status=1">Users List</a></li>
                <?php }
                if ($route_mg_change_password) {
                  MenuPermiss::ItemUrlOpen("change_pass.php", 'Change Password', 900, 650);
                }
                if ($route_mg_roles_permissions) { ?>
                  <li><a href="rolespermissions.php" target="_blank">Roles Permissions</a></li>
                <?php } ?>
              </ul>
            </li>
          <?php }
          if ($route_mg_survey) {
            MenuPermiss::ItemUrlPageSpan("survey.php", 'Broadcast Survey');
          }
          ?>
        </ul>
      </li>
      <?php if ($route_mg_events) { ?>
        <li style="background: #5daf5d;" title="Event Registrations" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-content="Click to view registrations for upcoming event"><a href="events_list.php">CPD EVENT</a></li>
      <?php } ?>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <?php if ($route_amendment_requests) { ?>
          <li><a href="amendment_requests.php"><?=$get_amendment_requests_count ? '<span class="badge badge-danger" style="background:red">' . $get_amendment_requests_count . '</span> ' : ''?>Amend Requests</a></li>
        <?php } ?>
        <li><a href="javascript:void(0)" onclick="document.getElementById('id01').style.display='block'"><span class="glyphicon glyphicon-log-in"></span> Sign out</a></li>
      </ul>
    </div>
  </div>
</nav>
<!--<div class="alert alert-success text-center" style="margin-bottom: 10px;margin-top: -20px;">LSUK will be on short break tomorrow from 7:00AM to 8:00AM. So the system will not be available for an hour. Thank you</div>-->
<!-- Modal to display record -->
<link rel="stylesheet" href="css/w3.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<div id="id01" class="w3-modal">
  <div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
    <header class="w3-container w3-blue">
      <span onclick="document.getElementById('id01').style.display='none'" class="w3-button w3-blue  w3-display-topright">&times;</span>
      <h4>Logout From LSUK</h4>
    </header>
    <div class="w3-container">
      <center>
        <h4>Are you sure to logout?</h4>
        <a href="logout.php"><button class="w3-button w3-blue">Log out<i class="w3-margin-left fa fa-lock"></i></button></a>
      </center><br>
    </div>
    <div class="w3-container w3-light-grey w3-padding">
      <button class="w3-button w3-right w3-white w3-border" onclick="document.getElementById('id01').style.display='none'">Close</button>
    </div>
  </div>
</div>
<script>
  function MM_openBrWindow(theURL, winName, features) {
    window.open(theURL, winName, features);
  }

  function popupwindow(url, title, w, h) {
    var left = (screen.width / 2) - (w / 2);
    var top = (screen.height / 2) - (h / 2);
    return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
  }
  // Get the modal
  var modal = document.getElementById('id01');

  // When the user clicks anywhere outside of the modal, close it
  window.onclick = function(event) {
    if (event.target == modal) {
      modal.style.display = "none";
    }
  }
  
	document.addEventListener('DOMContentLoaded', function () {
		// Get current page filename without query string
		var pathParts = window.location.pathname.split('/');
		var currentPage = pathParts[pathParts.length - 1].split('?')[0] || 'home.php';
		var links = document.querySelectorAll('nav li a');

		  links.forEach(function (link) {
			var matched = false;
			var href = link.getAttribute('href');
			var onClick = link.getAttribute('onClick') || '';

			// Match href value
			if (href && href !== '#' && href !== 'javascript:void(0)') {
			  var hrefPage = href.split('?')[0];
			  if (hrefPage === currentPage) {
				matched = true;
			  }
			}

			if (matched) {
			  // Add class to the current <li>
			  link.closest('li').classList.add('active');

			  // Add classes to parent dropdowns
			  var parent = link.closest('li');
			  while (parent) {
				if (parent.classList.contains('dropdown') || parent.classList.contains('dropdown-submenu')) {
				  parent.classList.add('btn-primary', 'active');
				}
				parent = parent.parentElement.closest('li');
			  }
			}
		  });
	});


  </script>
<!--End of modal-->