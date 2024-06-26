<?php
    require_once("inc/core.php");

    if($_SESSION["is_logged"])
    {
        header("Location: main");
    }
?>

<!doctype html>
<html lang="en">

<head>
    <title>Login</title>

    <?php include "inc/header_tags.php"; ?>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/login.css" rel="stylesheet">
</head>

<body class="text-center">
    <main class="form-signin">
        <form onclick="return false;">
            <div class="form-floating">
                <input name="username" type="text" class="form-control" id="floatingInput" placeholder="Username">
                <label for="floatingInput">Username</label>
            </div>
            <div class="form-floating">
                <input name="password" type="password" class="form-control" id="floatingPassword" placeholder="Password">
                <label for="floatingPassword">Password</label>
            </div>
            
            <button id="login" class="w-100 btn btn-lg btn-success">Login</button>
        </form>
    </main>

    <script src="js/jquery-3.6.0.min.js"></script>
    <script>
        let LOGIN_AJAX_BLOCK = false;

        $(() => {
            $("button#login").on("click", function(){
                if(LOGIN_AJAX_BLOCK) return;

                LOGIN_AJAX_BLOCK = true;

                $.ajax({
                    url    : "ajax.php",
                    method : "post",
                    data   : {
                        login    : 1,
                        username : $("input[name='username']").val(),
                        password : $("input[name='password']").val()
                    },
                    success : (data) => {
                        let data_json = JSON.parse(data);

                        if(data_json.status == 0)
                            alert("Invalid username or password.");
                        else
                            window.location = "main";

                        LOGIN_AJAX_BLOCK = false;
                    },
                    error : (err) => {
                        LOGIN_AJAX_BLOCK = false;
                        alert("An AJAX error occured. ("+err.status+" - "+err.statusText+")");
                    }
                });
            });
        });
    </script>
</body>
</html>