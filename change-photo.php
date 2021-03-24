<?php
include "classes/init.php";
$obj = new base_class();

if (isset($_POST['change_name'])) {

    $new_name = $obj->security($_POST['new_name']);
    //validation
    if (empty($new_name)) {
        $name_error = "Name field is required";
        $name_status = "";
    }else{
        $query = "UPDATE users SET name=? WHERE id=?";
        $obj->Normal_Query($query, [$new_name, $_SESSION['user_id']]);

        $obj->create_session("user_name", $new_name); //updaint the session
        $obj->create_session("name_updated", "Your name is successfully updated");
        header("location:index.php");

    }
        
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messenger</title>

    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />
    <link rel="preconnect" href="https://fonts.gstatic.com">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;900&family=Roboto:wght@100&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">



</head>

<body>
    <nav id="nav">

    </nav>
    <div class="chat-container ">
        <?php include "components/sidebar.php" ?>
        <!---close sidebar -->
        <section id="right-area">
            <form class="form-section" enctype="multipart/form-data" method="POST">
                <div class="group">
                    <h2 class="form-heading ">Change Photo</h2>
                </div>
                <div class="group">
                    <label for="change-image" id="change-image-label"></label>
                    <input type="file" name="change-image" id="change-image" class="change-img">
                </div>

                <div>
                    <input type="submit" name="change_img" class="btn signup-btn" value="Save changes">
                </div>
            </form>
        </section>
    </div>

    <?php include "components/js.php" ?>
</body>

</html>