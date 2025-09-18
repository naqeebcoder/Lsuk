<div class="col-md-12">
                <h2>Payment Terms</h2>
                <div class="form-group">
                  <select class="form-control validate[required]" name="payment_terms">
                    <option value="0">Pay Now</option>
                    <option value="7">7 Days</option>
                    <option value="21">21 Days</option>
                    <option value="28">28 Days</option>
                    <option value="35">35 Days</option>
                    <option value="42">42 Days</option>
                    <option value="49">49 Days</option>
                    <option value="56">56 Days</option>
                    <option value="63">63 Days</option>
                    <option value="63">63 Days</option>
                    <option value="70">70 Days</option>
                    <option value="77">77 Days</option>
                    <option value="84">84 Days</option>
                    <option value="91">91 Days</option>
                    <option value="98">98 Days</option>
                  </select>
                </div>
              </div>
              <?php 
            //   VALUE IN DATABASE
            	VAR payment_terms INT(3);
           $payment_terms=$row['payment_terms'];
          if($commit==0){
            $number=2;
            $divisor=7;
            $reminder=round($payment_terms/$divisor);
            $res=($reminder*2);
            $added_days=$payment_terms+$res;
            $dueDate=date("Y-m-d", strtotime($added_days.'days'));
            $nameOfDay = date('D', strtotime($dueDate));
            if($nameOfDay=='Sat'){
              $added_days=($added_days+2);
              $dueDate=date("Y-m-d", strtotime($added_days.'days'));
            }
            if($nameOfDay=='Sun'){
              $added_days=($added_days+1);
              $dueDate=date("Y-m-d", strtotime($added_days.'days'));
            }
          } 
          
        //   Putting in invoice due date
          if(isset($_POST['btn_commit']) && $commit==0){
            $dated=date("Y-m-d");
            $data = array('invoice_date'=>$dated,'invoice_amount'=>$total_amount,'commit'=>'1','status'=>'Sent','due_date'=>$dueDate,"inserted_by"=>$_SESSION['user_id'],"inserted_date"=>$dated);
          }
          ?>