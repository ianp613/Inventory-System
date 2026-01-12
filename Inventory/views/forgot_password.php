<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
        <link rel="stylesheet" href="../assets/fontawesome/css/font-awesome.min.css">
    <title>Inventory System - Account Recovery</title>
    <link rel="shortcut icon" href="../assets/img/logo-ico.png" type="image/x-icon">
</head>
  <body class="login" id="forgot_password">
    <div class="card-cover"></div>
    <div class="container">
      <div class="row justify-content-center align-items-center vh-100">
        <div class="col-md-4">
          <div class="card mt-3 mb-3">
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="card-body">
              <div class="row justify-content-center align-items-center">
                <img src="../assets/img/fposi-logo.png" style="width: 250px;" alt="fposi-logo.png">
              </div>
              <h5 class="card-title text-center mb-3">Account Recovery</h5>



                <div id="sent_to">
                  <div class="mb-3">
                    <label for="userid" class="form-label">Input User ID or Email</label>
                    <input type="userid" class="form-control" id="userid">
                  </div>
                  <div id="ready_state">
                    <button id="getcode_btn" type="submit" class="btn btn-dark w-100"><span class="fa fa-send"></span> Get Code</button>
                  </div>
                  <div id="sending_state" hidden>
                      <button class="btn btn-dark w-100" type="button">
                          <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                          Sending Code ...
                      </button>    
                  </div>
                </div>



                <div hidden id="confirm_code">
                  <div id="code_message" class="alert alert-primary text-center"></div>
                  <div class="mb-2">
                    <label for="code" class="form-label">Input Code</label>
                    <input type="text" class="form-control" id="code" maxlength="6">
                  </div>
                  <div class="mb-2">
                    <label for="new_password" class="form-label">New Password</label>
                    <input class="form-control" id="new_password" type="password">
                  </div>
                  <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input class="form-control" id="confirm_password" type="password">
                  </div>
                  <button id="login_btn" type="submit" class="btn btn-dark w-100"><span class="fa fa-send"></span> Submit</button>  
                </div>  
                <div class="w-100 text-center mt-3">Already have an account? <a class="text-dark" style="text-decoration: none;" href="login.php">Login Instead.</a></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <h6 class="copyright-1 f-10 text-secondary"><i>Copyright 2025 @ Wifi Team</i></h6>
  </body>
  
  <script src="../assets/js/bootstrap/bootstrap.min.js"></script>
  <script src="../assets/js/sole.js"></script>
  <script src="../assets/js/forgot_password.js"></script>
  <script src="../assets/js/script.js"></script>
  <script src="../assets/js/modal_alert.js"></script>
</html>