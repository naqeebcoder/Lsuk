<?php if(session_id() == '' || !isset($_SESSION)){session_start();}
if(empty($_SESSION['cust_UserName'])){
    echo '<script>window.location="index.php";</script>';
}
include 'source/db.php';
include 'source/class.php';
$page_name=$_GET['interp'];
$comp_id=$_SESSION['company_id'];
$get_child_companies=$acttObj->read_specific("*","child_companies","parent_comp=".$_SESSION['company_id']);
if($_SESSION['comp_type']==3 && empty($get_child_companies['id'])){
    echo "<script>window.location.href='".$page_name."?company_id=".base64_encode($_SESSION['company_id'])."';</script>";
}?> 
<!DOCTYPE HTML>
<head>
<?php include'source/header.php'; ?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
<style>.multiselect {min-width: 250px;}.multiselect-container {max-height: 400px;overflow-y: auto;max-width: 380px;}
.multiselect-container>li.active>a>label{color:white;}</style>
</head>

<body class="boxed">
<!-- begin container -->
<div id="wrap">
    <!-- begin header -->
<?php include'source/top_nav.php'; ?>
    <!-- end header -->
    
    <!-- begin page title -->
<section id="page-title">
        <div class="container clearfix">
            <h1>Place a Request</h1>
            <nav id="breadcrumbs">
                <ul>
                    <li><a href="index.php">Home</a> &rsaquo;</li>  
                </ul>
            </nav>
        </div>
    </section>
    <!-- begin page title -->
    
    <!-- begin content -->
    <section id="content" class="container clearfix">
        <!-- begin our company -->
 
    <!-- begin content -->
            <form class="sky-form" action="<?php echo $page_name; ?>" method="GET">
                <h4>Company (Organization), Unit or Team Name or Number  (Please Select From The List)</h4>
                <b> Select OR Write The Name Your Intended Team / Unit to Select From Suggested List e.g. Delivery Unit, Department, </b>
                <br><br>
                    <?php $user_code=$_SESSION['cust_userId'];
                    if($_SESSION['comp_nature']==1){
                        echo "<select id='company_id' name='company_id' required='' class='form-control multi_class'>";
                        // $query_super_company=$acttObj->read_all("parent_companies.sup_child_comp,comp_reg.name","parent_companies,comp_reg,company_login","parent_companies.sup_child_comp=comp_reg.id and parent_companies.sup_parent_comp=company_login.company_id and parent_companies.sup_parent_comp=".$_SESSION['company_id']);
                        // while($row_super_company=$query_super_company->fetch_assoc()){
                        //     echo "<optgroup label='".$row_super_company['name']."'>";
                        //     $single_company_query=$acttObj->read_all("comp_reg.id,comp_reg.name","child_companies,comp_reg","child_companies.child_comp=comp_reg.id AND child_companies.parent_comp=".$row_super_company['sup_child_comp']);
                        //     while($row_single_company=$single_company_query->fetch_assoc()){
                        //         echo "<option value='".base64_encode($row_single_company['id'])."'>".$row_single_company['name']."</option>";
                        //     }
                        //     echo "</optgroup>";
                        // }
                        $p_org_q = $acttObj->read_all("comp_reg.id,comp_reg.name", "comp_reg,subsidiaries", " comp_reg.id=subsidiaries.child_comp AND subsidiaries.parent_comp=$comp_id");
                        if(mysqli_num_rows($p_org_q)>0){
                                while($row_single_company=$p_org_q->fetch_assoc()){
                                echo "<option value='".base64_encode($row_single_company['id'])."'>".$row_single_company['name']."</option>";
                            }
                        }
                        // $p_org_ad = ($p_org_q != 0 ? " and comp_reg.id IN ($p_org_q) " : "");
                        echo "</select>";
                    }else if($_SESSION['comp_type']==2){
                        echo "<select id='company_id' name='company_id' required='' class='form-control multi_class'>";
                        $single_company_query=$acttObj->read_all("comp_reg.id,comp_reg.name","child_companies,comp_reg,company_login","child_companies.child_comp=comp_reg.id AND child_companies.parent_comp=company_login.company_id AND child_companies.parent_comp=".$_SESSION['company_id']);
                        while($row_single_company=$single_company_query->fetch_assoc()){
                            echo "<option value='".base64_encode($row_single_company['id'])."'>".$row_single_company['name']."</option>";
                        }
                        echo "</select>";
                    }else{
                      if(!empty($get_child_companies['id'])){
                        echo "<select id='company_id' name='company_id' required='' class='form-control multi_class'>";
                        $single_company_query=$acttObj->read_all("comp_reg.id,comp_reg.name","child_companies,comp_reg,company_login","child_companies.child_comp=comp_reg.id AND child_companies.parent_comp=company_login.company_id AND child_companies.parent_comp=".$_SESSION['company_id']);
                        $current_company_name=$acttObj->read_specific("name","comp_reg","id=".$_SESSION['company_id'])['name'];
                        echo "<option value='".base64_encode($_SESSION['company_id'])."'>".$current_company_name."</option>";
                        while($row_single_company=$single_company_query->fetch_assoc()){
                            echo "<option value='".base64_encode($row_single_company['id'])."'>".$row_single_company['name']."</option>";
                        }
                        echo "</select>";
                      }else{
                        $query_super_company=$acttObj->read_all("parent_companies.sup_child_comp,comp_reg.name","parent_companies,comp_reg,company_login","parent_companies.sup_child_comp=comp_reg.id and parent_companies.sup_parent_comp=company_login.company_id and parent_companies.sup_parent_comp=".$_SESSION['company_id']);
                        while($row_super_company=$query_super_company->fetch_assoc()){
                            echo "<optgroup label='".$row_super_company['name']."'>";
                            $single_company_query=$acttObj->read_all("comp_reg.id,comp_reg.name","child_companies,comp_reg","child_companies.child_comp=comp_reg.id AND child_companies.parent_comp=".$row_super_company['sup_child_comp']);
                            while($row_single_company=$single_company_query->fetch_assoc()){
                                echo "<option value='".base64_encode($row_single_company['id'])."'>".$row_single_company['name']."</option>";
                            }
                            echo "</optgroup>";
                        }
                      }
                    } ?>
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <br/>
                </form>
                        
    <!-- end content -->  
    <br/><br/><br/><br/>
        <hr>
        
        <!-- begin clients -->
       <?php include'source/our_client.php'; ?>
        <!-- end clients -->   
    </section>
    <!-- end content -->  
    
    <!-- begin footer -->
    <?php include'source/footer.php'; ?>
    <!-- end footer -->  
</div>
<!-- end container -->
</body>
<script src="../lsuk_system/js/jquery-1.11.3.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css"rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"type="text/javascript"></script>
<script type="text/javascript">
    $('.multi_class').multiselect({includeSelectAllOption: true,numberDisplayed: 1,enableFiltering: true,enableCaseInsensitiveFiltering: true});
</script>
</html>