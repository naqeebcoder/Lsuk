<?php
include '../../action.php';
//View active job edtails
if (isset($_POST['view_active_job_details']) && isset($_POST['ap_job_id']) && isset($_POST['ap_value'])) {
    $json = (object) null;
    $json->skip_client_signature = "0";
    $specific_interpreter_rates = array(4, 13);//Nadia and imran only
    if (isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])) {
        $table = $_POST['ap_value'];
        $update_id = $_POST['ap_job_id'];
        $row = $obj->read_specific("interpreter_reg.specific_agreed,$table.*,interpreter_reg.postCode as int_postcode,interpreter_reg.city as int_city,interpreter_reg.rph,interpreter_reg.rpm,interpreter_reg.rpu,interpreter_reg.email as interp_email,interpreter_reg.ratetravelworkmile,interpreter_reg.ratetravelexpmile", "$table,interpreter_reg", "$table.intrpName=interpreter_reg.id and $table.id=" . $update_id);
        $json->interpreter_postcode = $row['int_postcode'];
        $json->job_postcode = $row['postCode'];
        $intrpName = $row['intrpName'];
        $interp_email = $row['interp_email'];
        $interpreter_rate_id = $row['interpreter_rate_id'];
        $interpreter_rate_data = !empty($row['interpreter_rate_data']) ? (array) json_decode($row['interpreter_rate_data']) : array();
        if (!empty($interpreter_rate_data['id']) && in_array($intrpName, $specific_interpreter_rates)) {
            $interp_rph = $interpreter_rate_data['rate_value_f2f'] != 0 ? $interpreter_rate_data['rate_value_f2f'] : $row['rph'];
            $interp_rpm = $interpreter_rate_data['rate_value_tp'] != 0 ? $interpreter_rate_data['rate_value_tp'] : $row['rpm'];
            $interp_rpu = $interpreter_rate_data['rate_value_tr'] != 0 ? $interpreter_rate_data['rate_value_tr'] : $row['rpu'];
            $extra_title_parts = explode("-", $interpreter_rate_data['title']);
            $bookinType = trim($extra_title_parts[0]);
        } else {
            $interp_rph = $row['rph'];
            $interp_rpm = $row['rpm'];
            $interp_rpu = $row['rpu'];
            $bookinType = $row['bookinType'];
        }
        $interp_rph = $row['rph'];
        $interp_rpm = $row['rpm'];
        $interp_rpu = $row['rpu'];
        $hoursWorkd = $table == 'translation' ? $row['numberUnit'] : number_format($row['hoursWorkd'], 2);
        if ($table != 'translation') {
            $rateHour = $row['rateHour'];
        }
        if ($table == 'interpreter') {
            $rating_type = $obj->read_specific("rating_type", "job_ratings", "job_type=1 and job_id=" . $update_id)['rating_type'] ?: "0";
            $json->same_day_completed = $obj->read_specific("count(*) as counter", "$table", "cost_type IN ('dp','wp','mp') and assignDate='" . date('Y-m-d') . "' and deleted_flag=0 and order_cancel_flag=0")['counter'];
            if ($row['specific_agreed'] == '1' || ($row['int_city'] != $row['assignCity'])) {
                $json->is_travel_time = "1";
            } else {
                if ($row['int_city'] == $row['assignCity']) {
                    $json->interpreter_postcode = $row['int_postcode'];
                    $json->job_postcode = $row['postCode'];
                }
                $json->is_travel_time = "0";
            }
            if ($row['postCode'] && !empty($row['postcode_data'])) {
                $postcode_data = explode(',', $row['postcode_data']);
                $json->latitude = $postcode_data[0];
                $json->longitude = $postcode_data[1];
            } else {
                $json->api_key = "JwJX4MXnkEihbeI4wAPTIg14351";
                $json->latitude = "";
                $json->longitude = "";
            }
            $travelMile = $row['travelMile'];
            $rateMile = $row['ratetravelexpmile'];
            $chargeTravel = $row['chargeTravel'];
            $travelCost = $row['travelCost'];
            $otherCost = $row['otherCost'];
            $travelTimeHour = $row['travelTimeHour'];
            $travelTimeRate = $row['ratetravelworkmile'];
            $chargeTravelTime = $row['chargeTravelTime'];
            $rate_per_hour = $interp_rph;
        }
        if ($table == 'telephone') {
            $rating_type = $obj->read_specific("rating_type", "job_ratings", "job_type=2 and job_id=" . $update_id)['rating_type'] ?: "0";
            $calCharges = $row['calCharges'];
            $otherCharges = $row['otherCharges'];
            $rate_per_minute = $interp_rpm;
        }
        if ($table == 'translation') {
            $rating_type = $obj->read_specific("rating_type", "job_ratings", "job_type=3 and job_id=" . $update_id)['rating_type'] ?: "0";
            $docType = $row['docType'];
            $numberUnit = $row['numberUnit'];
            $rpU = $row['rpU'];
            $otherCharg = $row['otherCharg'];
            $rate_per_unit = $interp_rpu;
        }
        $array_rating_type = array(0 => "No rating yet", 1 => "Already rated", 2 => "Remind later", 3 => "Don't ask again");
        $json->rating_type = $rating_type;
        $json->rating_type_label = $array_rating_type[$rating_type];
        $chargInterp = $table == 'translation' ? number_format($numberUnit * $rpU) : $row['chargInterp'];
        $deduction = $row['deduction'];
        $total_charges_interp = $row['total_charges_interp'];
        $wt_tm = $row['wt_tm'];
        $st_tm = $row['st_tm'];
        $fn_tm = $row['fn_tm'];
        $assignDate = $table == 'translation' ? $row['asignDate'] : $row['assignDate'];
        $assignTime = $table == 'translation' ? '00:00:00' : $row['assignTime'];
        $assignDur = $table == 'translation' ? '0' : $row['assignDur'];
        $expected_start = date($assignDate . ' ' . substr($assignTime, 0, 5));
        $expected_end = date("Y-m-d H:i", strtotime("+$assignDur minutes", strtotime($expected_start)));
        if ($assignDur > 60) {
            $hours = $assignDur / 60;
            if (floor($hours) > 1) {
                $hr = "hours";
            } else {
                $hr = "hour";
            }
            $mins = $assignDur % 60;
            if ($mins == 00) {
                $get_dur = sprintf("%2d $hr", $hours);
            } else {
                $get_dur = sprintf("%2d $hr %02d minutes", $hours, $mins);
            }
        } else if ($assignDur == 60) {
            $get_dur = "1 Hour";
        } else {
            $get_dur = $assignDur . " minutes";
        }
        $first_time = $row['wt_tm'] != '1001-01-01 00:00:00' ? $row['wt_tm'] : $row['st_tm'];
        if (($table != 'translation' && $hoursWorkd == 0) || ($table == 'translation' && $numberUnit == 0)) {
            $row['hours_filled'] = 0;
        } else {
            $row['hours_filled'] = 1;
        }
        $row['wait_time_filled'] = $row['wt_tm'] == '1001-01-01 00:00:00' ? 0 : $row['wt_tm'];
        $row['start_time_filled'] = $row['st_tm'] == '1001-01-01 00:00:00' ? 0 : $row['st_tm'];

        if ($row['fn_tm'] == '1001-01-01 00:00:00') {
            $row['finish_time_filled'] = 0;
            $hour_calculated = 0;
        } else {
            $row['finish_time_filled'] = $row['fn_tm'];
            //when uploaded start and end then display calculated hours
            $last_time = $row['fn_tm'];
            $hour_calculated = $hoursWorkd;
        }
        $valid_check_q = $obj->read_specific("id", "$table", "intrpName=" . $_POST['ap_user_id'] . " and id=" . $update_id);
        $valid_check = $valid_check_q != '' ? 'yes' : 'no';
        if ($valid_check == 'no') {
            $row['valid'] = 0;
            $json->msg = 'You are not allowed to open this job!';
        } else {
            $row['valid'] = 1;
        }
        if (date('Y-m-d H:i', strtotime($row['assignDate'] . ' ' . $row['assignTime'])) > date('Y-m-d H:i')) {
            $row['problem_hours'] = 1;
            $json->msg = "This job can't be started before " . date("d-m-Y H:i", strtotime($expected_start)) . " ! Thank you";
        } else if ($row['deleted_flag'] == 1 || $row['order_cancel_flag'] == 1 || $row['orderCancelatoin'] == 1 || $row['intrp_salary_comit'] == 1) {
            $row['problem_hours'] = 1;
            $json->msg = 'This job is not available now! Thank you';
        } else {
            $row['problem_hours'] = 0;
            $json->msg = '';
        }
        $json->client_reference = $row['orgRef'];
        $json->client_name = $row['orgContact'];
        $json->booking_person_name = $row['inchPerson'];
        $json->company_name = $obj->read_specific("name", "comp_reg", "abrv='" . $row['orgName'] . "'")['name'];
        $json->address = $table == "interpreter" ? trim($row['buildingName']) . ' ' . trim($row['street']) . ' ' . trim($row['assignCity']) . ' ' . trim($row['postCode']) : "";
        if ($table == 'interpreter') {
            $check_existing_uploads = $obj->read_specific("id", "job_files", "tbl='".$table."' AND order_id=" . $update_id . " AND interpreter_id=" . $_POST['ap_user_id'] . " AND file_type='timesheet'")['id'];
            $json->attachment_uploaded = !empty($row['parking_tickets']) || !empty($check_existing_uploads) ? 1 : 0;
            $json->assignCity = $row['assignCity'];
            $json->assignDate = $misc->dated($row['assignDate']);
            $json->job_key = $row['nameRef'];
            $json->feedback = $obj->read_specific("count(*)", "interp_assess", "interpName='id-" . $_POST['ap_user_id'] . "' AND table_name='interpreter' AND order_id=" . $update_id)['count(*)'];
            $json->hours_worked = ($hoursWorkd);
            $json->rate_per_hour = ($rate_per_hour);
            $json->charge_for_interpreting_time = ($chargInterp);
            $json->travel_time_hours = ($travelTimeHour);
            $json->travel_time_rate_per_hour = ($travelTimeRate);
            $json->charge_for_travel_time = ($chargeTravelTime);
            $json->travel_mile = ($travelMile);
            $json->rate_per_mileage = ($rateMile);
            $json->charge_for_travel_cost = ($chargeTravel);
            $json->travel_cost = ($travelCost);
            $json->other_cost = ($otherCost);
            $json->deduction = ($deduction);
            $json->job_type = 'Face To Face';
            $json->job_id = $row['id'];
            $json->max_rate = $row['source'] != "Sign Language (BSL)" ? "40" : "1000";
        }
        if ($table == 'telephone') {
            $json->assignDate = $misc->dated($row['assignDate']);
            $json->job_type = 'Telephone';
            $json->job_id = $row['id'];
            $json->job_key = $row['nameRef'];
            $json->hours_worked = ($hoursWorkd);
            $json->rate_per_minute = $rate_per_minute;
            $json->charge_for_interpreting_time = $chargInterp;
            $json->call_charges = ($calCharges);
            $json->other_charges = ($otherCharges);
            $json->deduction = ($deduction);
            $json->max_rate = $row['source'] != "Sign Language (BSL)" ? "0.75" : "1000";
        }
        if ($table == 'translation') {
            $json->assignDate = $misc->dated($row['asignDate']);
            $json->job_type = 'Translation';
            $json->job_id = $row['id'];
            $json->job_key = $row['nameRef'];
            $json->units = ($numberUnit);
            $json->rate_per_unit = ($rate_per_unit);
            $json->total_cost = $chargInterp;
            $json->any_other_charges = ($otherCharg);
            $json->deduction = ($deduction);
            $json->docType = ($docType);
            $json->delivery_date = $misc->dated($row['deliverDate2']);
            $json->document_type = $obj->read_specific("trans_cat.tc_title as document_type", "trans_cat", "trans_cat.tc_id IN (" . $row['docType'] . ")")['document_type'];
            $json->category = $obj->read_specific("GROUP_CONCAT(trans_types.tt_title SEPARATOR ', ') as category", "trans_types", "trans_types.tt_id IN (" . $row['transType'] . ")")['category'];
            $json->max_rate = $row['source'] != "Sign Language (BSL)" ? "0.20" : "1000";
        }
        $json->total_charges = ($total_charges_interp);
        $json->interpreter_email = $interp_email;
        $json->source = $row['source'];
        $json->target = $row['target'];
        if ($table != 'translation') {
            $json->expected_start = date("d-m-Y H:i", strtotime($expected_start));
            $json->expected_end = date("d-m-Y H:i", strtotime($expected_end));
            $json->expected_duration = trim($get_dur);
            $json->hour_calculated = number_format($hour_calculated);
            $json->wait_time_filled = $row['wait_time_filled'] != 0 ? date("d-m-Y H:i", strtotime($row['wait_time_filled'])) : strval($row['wait_time_filled']);
            $json->start_time_filled = $row['start_time_filled'] != 0 ? date("d-m-Y H:i", strtotime($row['start_time_filled'])) : strval($row['start_time_filled']);
            $json->finish_time_filled = $row['finish_time_filled'] != 0 ? date("d-m-Y H:i", strtotime($row['finish_time_filled'])) : strval($row['finish_time_filled']);
            if ($hour_calculated != 0) {
                if ($table == 'telephone') {
                    $hours_done = $hour_calculated;
                } else {
                    $hours_done = $hour_calculated * 60;
                }
                if ($hours_done > 60) {
                    $hours_c = $hours_done / 60;
                    if (floor($hours_c) > 1) {
                        $hr_c = "hours";
                    } else {
                        $hr_c = "hour";
                    }
                    $mins_c = $hours_done % 60;
                    if ($mins_c == 00) {
                        $get_dur_c = sprintf("%2d $hr_c", $hours_c);
                    } else {
                        $get_dur_c = sprintf("%2d $hr_c %02d minutes", $hours_c, $mins_c);
                    }
                } else if ($hours_done == 60) {
                    $get_dur_c = "1 Hour";
                } else {
                    $get_dur_c = number_format($hours_done) . " minutes";
                }
                $json->duration_worked = $get_dur_c;
            } else {
                $json->duration_worked = "Not filled yet";
            }
        }
        $step_1_completed = "0";
        $step_1_enabled = "0";
        $step_2_completed = "0";
        $step_2_enabled = "0";
        $step_3_completed = "0";
        $step_3_enabled = "0";
        $step_4_completed = "0";
        $step_4_enabled = "0";
        $json->assignment_expired = "1"; //To be reverted back to 0
        if ($table == "translation") {
            $json->skip_client_signature = "1";
            if ($row['hours_filled'] == 0 || $row['total_charges_interp'] == "0") {
                $step_1_completed = "0";
                $step_1_enabled = "1";
            } else if (($row['hours_filled'] != 0 && $row['int_sig'] == "") || ($row['int_sig'] == "" && $json->skip_client_signature == "1")) {
                $step_1_completed = "1";
                $step_2_completed = "1";
                $step_3_enabled = "1";
            } else {
                $step_1_completed = "1";
                $step_2_completed = "1";
                $step_3_completed = "1";
                $step_4_completed = "1";
                $step_4_enabled = "1";
            }
        } else if ($table == "telephone") {
            $json->skip_client_signature = "1";
            $expected_new_duration = $assignDur * 5;
            $expected_duration_end = date("Y-m-d H:i", strtotime("+$expected_new_duration minutes", strtotime($expected_start)));
            $json->expected_end_after_duration = date("d-m-Y H:i", strtotime($expected_duration_end));
            if ($row['assignDate'] < date('Y-m-d') || ($row['assignDate'] == date('Y-m-d') && $expected_duration_end < date('Y-m-d H:i'))) {
                $json->assignment_expired = "1";
            }
            if ($row['finish_time_filled'] == 0 || $row['hours_filled'] == 0 || $row['total_charges_interp'] == "0") {
                $step_1_completed = "0";
                $step_1_enabled = "1";
            } else if (($row['hours_filled'] != 0 && $row['int_sig'] == "") || ($row['int_sig'] == "" && $json->skip_client_signature == "1")) {
                $step_1_completed = "1";
                $step_2_completed = "1";
                $step_3_enabled = "1";
            } else {
                $step_1_completed = "1";
                $step_2_completed = "1";
                $step_3_completed = "1";
                $step_4_completed = "1";
                $step_4_enabled = "1";
            }
        } else {
            $json->skip_client_signature = "0";
            $expected_new_duration = $assignDur * 2;
            $expected_duration_end = date("Y-m-d H:i", strtotime("+$expected_new_duration minutes", strtotime($expected_start)));
            $json->expected_end_after_duration = date("d-m-Y H:i", strtotime($expected_duration_end));
            if ($row['assignDate'] < date('Y-m-d') || ($row['assignDate'] == date('Y-m-d') && $expected_duration_end < date('Y-m-d H:i'))) {
                $json->skip_client_signature = "1";
                $json->assignment_expired = "1";
            }
            if ($row['finish_time_filled'] == "0" || $row['hours_filled'] == "0" || $row['total_charges_interp'] == "0") {
                $step_1_completed = "0";
                $step_1_enabled = "1";
            } else if ($row['cl_sig'] == "" && $json->skip_client_signature == "0") {
                $step_1_completed = "1";
                $step_2_enabled = "1";
            } else if (($row['cl_sig'] != "" && $row['int_sig'] == "") || ($row['int_sig'] == "" && $json->skip_client_signature == "1")) {
                $step_1_completed = "1";
                $step_2_completed = "1";
                $step_3_enabled = "1";
            } else {
                $step_1_completed = "1";
                $step_2_completed = "1";
                $step_3_completed = "1";
                $step_4_completed = "1";
                $step_4_enabled = "1";
            }
        }

        $json->steps = array(
            "step 1" => array("is_completed" => $step_1_completed, "is_enabled" => $step_1_enabled),
            "step 2" => array("is_completed" => $step_2_completed, "is_enabled" => $step_2_enabled),
            "step 3" => array("is_completed" => $step_3_completed, "is_enabled" => $step_3_enabled),
            "step 4" => array("is_completed" => $step_4_completed, "is_enabled" => $step_4_enabled)
        );
        $json->hours_filled = strval($row['hours_filled']);
        if ($table == 'interpreter') {
            $json->client_signature = $row['cl_sig'];
            $json->cl_sign_date = $row['cl_sign_date'] ?: "";
        }
        $json->interpreter_signature = $row['int_sig'];
        $json->int_sign_date = $row['int_sign_date'] ?: "";
        $json->problem_hours = strval($row['problem_hours']);
        $json->valid = strval($row['valid']);
        $json->bonus_amount = strval(0.5);
    } else {
        $json->msg = "not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}