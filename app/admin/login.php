<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>

    <?php include 'inc_head.php'?>
    <link href="css/login_.css?v=<?php echo time();?>" rel="stylesheet">


</head>
<body>

<!-- <div class="container-fluid"> -->

<div class="login-wrap">
	<div class="login-html">
		<input id="tab-1" type="radio" name="tab" class="sign-in" checked><label for="tab-1" class="tab">Sign In</label>
		<input id="tab-2" type="radio" name="tab" class="for-pwd"><label for="tab-2" class="tab">Forgot Password</label>
		<div class="login-form">

			<div class="sign-in-htm">
                <form id="loginForm" action="" method="post">
                    <div class="group">
                        <label for="email" class="label">Username or Email</label>
                        <input id="email" type="text" class="input">
                    </div>
                    <div class="group" style="position: relative;">
                        <label for="password" class="label">Password</label>
                        <input id="password" type="password" class="input" data-type="password">
                        <span class="" style="position: absolute; top: 34px; right: 20px;" id="togglePassword">
                            <i class="fas fa-eye-slash"></i>
                        </span>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="remember">
                        <label class="form-check-label" for="remember">Remember</label>
                    </div>
                    <div class="group">
                        <input type="submit" class="button" value="LOGIN">
                    </div>
                    <div style="text-align: center; display: flex; justify-content: space-around;">
                        <div>
                            <a href="../register.php">Sign up</a>
                        </div>
                        <div>
                            <a href="../index.php">Trandar store</a>
                        </div>
                        
                    </div>
                    <!-- <div style="text-align: center;">
                        <a href="../../index.php">Trandar store</a>
                    </div> -->
                    <div class="hr"></div>
                </form>
			</div>

			<div class="for-pwd-htm">
				<div class="group">
					<label for="" class="label">Username or Email</label>
					<input id="" type="text" class="input">
				</div>
				<div class="group">
					<input type="submit" class="button" value="Reset Password">
				</div>
				<div class="hr"></div>
			</div>

		</div>
	</div>
</div>

<!-- </div> -->

    
    <script src="js/login_.js?v=<?php echo time();?>"></script>

</body>
</html>