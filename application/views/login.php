<?php
$url = base_url();
$main_url = explode('/', $url);
array_pop($main_url);
$main_url1 = implode('/', $main_url);
$main_url = explode('/', $main_url1);
array_pop($main_url);
$main_url =  implode('/', $main_url) . '/';
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>GST Billing - Sign In</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.4/css/bootstrap.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/sb-admin.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/style.css?v=3" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.4/js/bootstrap.min.js"></script>

    <style>
        body {
            background: radial-gradient(circle at center, #161e31 0%, #0a0d15 100%) !important;
            font-family: 'Outfit', sans-serif !important;
            color: #ffffff !important;
            min-height: 100vh !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 !important;
            overflow-x: hidden !important;
            position: relative !important;
        }

        /* Animated background elements for premium feel */
        .bg-circle {
            position: absolute !important;
            border-radius: 50% !important;
            filter: blur(120px) !important;
            z-index: 0 !important;
            pointer-events: none !important;
            opacity: 0.25 !important;
        }
        .bg-circle-1 {
            width: 500px !important;
            height: 500px !important;
            top: -150px !important;
            left: -150px !important;
            background: rgba(60, 141, 188, 0.4) !important; /* Brand Blue */
        }
        .bg-circle-2 {
            width: 600px !important;
            height: 600px !important;
            bottom: -200px !important;
            right: -150px !important;
            background: rgba(39, 174, 96, 0.3) !important; /* Brand Green */
        }

        .container {
            z-index: 1 !important;
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            width: 100% !important;
        }

        .login-card {
            background: rgba(22, 30, 49, 0.65) !important;
            backdrop-filter: blur(25px) !important;
            -webkit-backdrop-filter: blur(25px) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            border-radius: 24px !important;
            padding: 45px 35px !important;
            width: 100% !important;
            max-width: 420px !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important;
            transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease !important;
        }
        .login-card:hover {
            transform: translateY(-2px) !important;
            border-color: rgba(60, 141, 188, 0.35) !important;
            box-shadow: 0 30px 60px -10px rgba(0, 0, 0, 0.6) !important;
        }

        .logo-container {
            text-align: center !important;
            background: #ffffff !important;
            border-radius: 14px !important;
            padding: 14px 20px !important;
            display: inline-block !important;
            margin: 0 auto 35px auto !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
            width: 100% !important;
        }
        .logo-container img {
            max-height: 55px !important;
            width: auto !important;
            object-fit: contain !important;
            display: block !important;
            margin: 0 auto !important;
        }

        .form-group {
            margin-bottom: 24px !important;
            position: relative !important;
        }
        .form-group label {
            font-size: 12.5px !important;
            font-weight: 600 !important;
            color: rgba(255, 255, 255, 0.7) !important;
            margin-bottom: 8px !important;
            display: block !important;
            text-transform: uppercase !important;
            letter-spacing: 0.8px !important;
        }

        .form-control {
            background: rgba(10, 13, 22, 0.4) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-radius: 12px !important;
            padding: 12px 16px !important;
            height: auto !important;
            color: #ffffff !important;
            font-size: 14.5px !important;
            transition: all 0.3s ease !important;
            width: 100% !important;
        }
        .form-control:focus {
            background: rgba(10, 13, 22, 0.6) !important;
            border-color: #3c8dbc !important;
            box-shadow: 0 0 10px rgba(60, 141, 188, 0.3) !important;
            outline: none !important;
        }
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.3) !important;
        }

        /* High-contrast brand green button */
        .btn-signin {
            background: linear-gradient(135deg, #00a65a 0%, #008d4c 100%) !important;
            border: none !important;
            border-radius: 12px !important;
            padding: 14px !important;
            color: #ffffff !important;
            font-size: 16px !important;
            font-weight: 600 !important;
            width: 100% !important;
            transition: all 0.3s ease !important;
            cursor: pointer !important;
            margin-top: 15px !important;
            box-shadow: 0 4px 15px rgba(0, 166, 90, 0.3) !important;
            letter-spacing: 0.5px !important;
        }
        .btn-signin:hover {
            background: linear-gradient(135deg, #00be68 0%, #00a056 100%) !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 20px rgba(0, 166, 90, 0.45) !important;
        }
        .btn-signin:active {
            transform: translateY(0) !important;
        }

        .links-container {
            display: flex !important;
            flex-direction: column !important;
            gap: 12px !important;
            align-items: center !important;
            margin-top: 24px !important;
        }
        .link-item {
            color: rgba(255, 255, 255, 0.6) !important;
            text-decoration: none !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            transition: color 0.2s ease !important;
        }
        .link-item:hover {
            color: #ffffff !important;
            text-decoration: underline !important;
        }

        /* Modern Alert styling */
        .alert {
            border-radius: 12px !important;
            border: none !important;
            font-size: 14px !important;
            padding: 14px 18px !important;
            margin-bottom: 24px !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2) !important;
        }
        .alert-success {
            background: rgba(46, 204, 113, 0.2) !important;
            border-left: 4px solid #2ecc71 !important;
            color: #2ecc71 !important;
        }
        .alert-info {
            background: rgba(52, 152, 219, 0.2) !important;
            border-left: 4px solid #3498db !important;
            color: #3498db !important;
        }
        .alert .close {
            color: inherit !important;
            opacity: 0.8 !important;
            text-shadow: none !important;
            font-size: 20px !important;
            line-height: 1 !important;
        }

        /* Glassmorphism modal styling */
        .modal-content {
            background: rgba(10, 13, 22, 0.95) !important;
            backdrop-filter: blur(25px) !important;
            -webkit-backdrop-filter: blur(25px) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-radius: 20px !important;
            color: #ffffff !important;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5) !important;
        }
        .modal-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
            padding: 20px 24px !important;
        }
        .modal-title {
            font-weight: 600 !important;
            font-size: 19px !important;
            letter-spacing: 0.5px !important;
        }
        .modal-body {
            padding: 24px !important;
        }
        .modal-body p {
            color: rgba(255, 255, 255, 0.7) !important;
            font-size: 14px !important;
            margin-bottom: 20px !important;
        }
        .modal-footer {
            border-top: none !important;
            padding: 0 24px 24px 24px !important;
            display: flex !important;
            justify-content: flex-end !important;
            gap: 12px !important;
        }
        .btn-cancel {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #ffffff !important;
            border-radius: 10px !important;
            padding: 10px 20px !important;
            font-weight: 500 !important;
            font-size: 14px !important;
            transition: all 0.2s ease !important;
            cursor: pointer !important;
        }
        .btn-cancel:hover {
            background: rgba(255, 255, 255, 0.12) !important;
        }
        .btn-send {
            background: linear-gradient(135deg, #3c8dbc 0%, #2b6b90 100%) !important;
            border: none !important;
            color: #ffffff !important;
            border-radius: 10px !important;
            padding: 10px 20px !important;
            font-weight: 600 !important;
            font-size: 14px !important;
            box-shadow: 0 4px 12px rgba(60, 141, 188, 0.3) !important;
            transition: all 0.2s ease !important;
            cursor: pointer !important;
        }
        .btn-send:hover {
            background: linear-gradient(135deg, #4da3d4 0%, #357fa9 100%) !important;
            box-shadow: 0 6px 15px rgba(60, 141, 188, 0.5) !important;
            transform: translateY(-1px) !important;
        }
    </style>
</head>

<body>
    <!-- Background blur effects -->
    <div class="bg-circle bg-circle-1"></div>
    <div class="bg-circle bg-circle-2"></div>

    <div class="container">
        <div class="login-card">
            <!-- Start Flash Message -->
            <?php if ($this->session->flashdata('SUCCESSMSG')) { ?>
                <div role="alert" class="alert alert-success alert-dismissible fade show">
                    <button data-dismiss="alert" class="close" type="button" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <strong>Success!!</strong> <?= $this->session->flashdata('SUCCESSMSG') ?>
                </div>
            <?php } ?>

            <?php if ($this->session->flashdata('INFOMSG')) { ?>
                <div role="alert" class="alert alert-info alert-dismissible fade show">
                    <button data-dismiss="alert" class="close" type="button" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <strong>Info!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                </div>
            <?php } ?>
            <!-- End Flash Message -->

            <div class="logo-container">
                <?php 
                $logo = !empty($company_logo['company_logo']) ? ltrim($company_logo['company_logo'], './') : '';
                ?>
                <?php if($logo): ?>
                    <img src="<?php echo base_url(); ?><?php echo $logo; ?>" alt="Company Logo" />
                <?php else: ?>
                    <img src="<?php echo base_url(); ?>dist/img/a3.jpg" alt="Default Logo" />
                <?php endif; ?>
            </div>

            <form method="post" action="<?php echo base_url(); ?>LoginController/login_user" role="login">
                <div class="form-group">
                    <label for="user_email">Email Address</label>
                    <input class="form-control" id="user_email" name="user_email" type="email"
                        placeholder="Enter your email" required
                        pattern="^([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})$" />
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input class="form-control" id="password" type="password" name="password"
                        placeholder="Enter your password" required>
                </div>

                <div class="pwstrength_viewport_progress"></div>

                <button type="submit" class="btn btn-signin" id="signin">Sign In</button>

                <div class="links-container">
                    <a href="#" id="forgot-password-link" class="link-item">
                        Forgot Password?
                    </a>
                    
                    <a href="<?php echo $main_url; ?>" class="link-item">
                        Go To Home
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" role="dialog" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="forgotPasswordModalLabel">Reset Password</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Enter your registered email address to receive a new password:</p>
                    <form method="post" action="<?php echo base_url(); ?>LoginController/forgot_password" id="forgotPasswordForm">
                        <div class="form-group">
                            <input type="email" name="to_email" class="form-control"
                                placeholder="Enter your email address" required
                                pattern="^([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})$">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-cancel" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-send" id="sendPasswordBtn">Send New Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Fade out custom loader if present
            $(".div_load").fadeOut(4000);

            // Forgot password link click
            $("#forgot-password-link").click(function(e) {
                e.preventDefault();
                $("#forgotPasswordModal").modal('show');
            });

            // Handle forgot password form submission
            $("#forgotPasswordForm").submit(function(e) {
                var email = $("input[name='to_email']").val();
                if (email.trim() === '') {
                    alert("Please enter your email address");
                    return false;
                }

                // Show loading state
                $("#sendPasswordBtn").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...');
                $("#sendPasswordBtn").prop('disabled', true);

                // Form will submit normally
                return true;
            });

            // Reset button state when modal is closed
            $('#forgotPasswordModal').on('hidden.bs.modal', function() {
                $("#sendPasswordBtn").html('Send New Password');
                $("#sendPasswordBtn").prop('disabled', false);
                $("#forgotPasswordForm")[0].reset();
            });

            // Focus email field when modal opens
            $('#forgotPasswordModal').on('shown.bs.modal', function() {
                $("input[name='to_email']").focus();
            });
        });
    </script>
</body>

</html>