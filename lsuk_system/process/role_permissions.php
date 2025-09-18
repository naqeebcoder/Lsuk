<?php
session_start();
include '../actions.php';
//Admin manage user accessibles
if (isset($_POST["request_user_accessibles"]) && isset($_POST['user_id'])) {
    $row_user = $obj->read_specific("login.*,rolenamed.id as role_id,rolenamed.named as title", "login,rolenamed", "login.prv=rolenamed.named AND login.id=" . $_POST['user_id']); ?>
    <center>
        <h3><label class="label label-info"><?php echo "Staff User : <b>" . $row_user['name'] . "</b>"; ?></label><label class="label label-primary pull-right"><?php echo "<b>Department # (" . $row_user['title'] . ")</b>"; ?></label></h3>
        <p>Account created on : <?php echo $misc->dated($row_user['dated']); ?></p>
    </center>
    <style>
        .btn_action {
            margin: 3px 5px 3px 0;
        }
        .route-heading {
            background: #f7f7f7;
            padding: 8px 12px;
            font-weight: bold;
            border-left: 4px solid #007bff;
            margin-top: 12px;
            border-radius: 4px;
        }
        .route-label {
            margin: 12px 0 5px 15px;
            font-weight: 600;
            color: #444;
        }
        .actions-block {
            margin-left: 30px;
            margin-bottom: 10px;
        }
    </style>
    <table class="table table-bordered">
        <tbody>
            <tr>
                <td colspan="2"><?php if ($row_user['role_id'] != "1830") {
                        $get_routes = $obj->read_all("DISTINCT routes.*", "route_actions,routes,route_permissions", "route_actions.route_id=routes.id AND routes.id=route_permissions.perm_id AND routes.route_deleted=0 AND route_permissions.role_id=" . $row_user['role_id']." ORDER BY TRIM(route_heading) ASC");
                        if ($get_routes->num_rows > 0) {
                            $array_routes = [];
                            while ($row_route = $get_routes->fetch_assoc()) {
                                $array_routes[$row_route['route_heading']][] = $row_route;
                            }

                            echo "<ul class='list-group'>";
                            foreach ($array_routes as $heading => $routes_under_heading) {
                                echo "<li class='list-group-item'>";
                                echo "<div class='route-heading'>" . htmlspecialchars($heading) . "</div>";

                                foreach ($routes_under_heading as $row_route) {
                                    $label = $row_route['label'];
                                    if ($label === "Add New Invoice") {
                                        $label = "Add New Company Income Invoice";
                                    } elseif ($label === "Invoices List") {
                                        $label = "Company Income Invoice List";
                                    }

                                    echo "<div class='route-label'>" . $label . "</div>";
                                    echo "<div class='actions-block'>";
                                    $get_actions = $obj->read_all("*", "route_actions", "route_id=" . $row_route['id']." ORDER BY TRIM(action_name) ASC");
                                    if ($get_actions->num_rows > 0) {
                                        while ($row_actions = $get_actions->fetch_assoc()) {
                                            $has_action = $obj->read_specific("*", "action_permissions", "user_id=" . $row_user['id'] . " AND action_id=" . $row_actions['id']);
                                            $append_checked = !empty($has_action['id']) ? " checked" : "";
                                            $append_click = 'onclick="update_accessible_action(this,' . $row_user['id'] . ',' . $row_actions['id'] . ',1)"';
                                            ?>
                                            <label class="btn btn-sm btn-default btn_action">
                                                <input <?php echo $append_click . $append_checked; ?> 
                                                    type="checkbox" 
                                                    value="<?php echo $row_actions['id']; ?>" 
                                                    data-value="<?php echo $row_user['id']; ?>" /> 
                                                <?php echo $row_actions['action_name']; ?>
                                            </label>
                                            <?php
                                        }
                                    }
                                    $get_actions_extra = $obj->read_all("*", "route_actions_extra", "route_id=" . $row_route['id']);
                                    if ($get_actions_extra->num_rows > 0) {
                                        while ($row_actions_extra = $get_actions_extra->fetch_assoc()) {
                                            $has_action_extra = $obj->read_specific("*", "action_permissions_extra", "user_id=" . $row_user['id'] . " AND action_id=" . $row_actions_extra['id']);
                                            $append_checked_extra = !empty($has_action_extra['id']) ? " checked" : "";
                                            $append_click_extra = 
'onclick="update_accessible_action(this,' . $row_user['id'] . ',' . $row_actions_extra['id'] . ',2)"';
?>
                                            <label class="btn btn-sm btn-danger btn_action"><input <?php 
echo $append_click_extra . $append_checked_extra; ?> type="checkbox" value="<?php echo 
$row_actions_extra['id']; ?>" data-value="<?php echo $row_user['id']; ?>" /> <?php echo $row_actions_extra['action_name']; ?> </label>
                                            <?php }
                                    }
                                    echo "</div>";
                                }
                                echo "</li>";
                            }
                            echo "</ul>";
                        } else {
                            echo "<center><i>No routes found!</i></center>";
                        }
                    } else {
                        echo "<center><i>No need to update this for Management Department!</i></center>";
                    } ?>
                </td>
            </tr>
        </tbody>
    </table>
<?php }
//Admin update user accessibles action
if (isset($_POST["update_accessible_action"]) && isset($_POST['user_id']) && isset($_POST['action_id']) && isset($_POST['type'])) {
    $table = $_POST["action_for"] == 1 ? "action_permissions" : "action_permissions_extra";
    if ($_POST['type'] == "insert") {
        $obj->insert("$table", ["user_id" => $_POST['user_id'], "action_id" => $_POST['action_id'], "created_date" => date('Y-m-d H:i:s')]);
        $response['status'] = "action_allowed";
    } else {
        $obj->delete("$table", "user_id=" . $_POST['user_id'] . " AND action_id=" . $_POST['action_id']);
        $response['status'] = "action_blocked";
    }
    echo json_encode($response);
    exit;
}
