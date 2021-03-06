<?php
include "init.php";
//if a use is already logged n , he will have a $_SESSION['user_id], then we will redirect to home page
if (isset($_SESSION['user_id'])) {

    header("location:index.php");
}
//Login starts
$obj = new base_class();
if (isset($_POST['login'])) {

    $email     =   $obj->security($_POST['email']);
    $password  =   $obj->security($_POST['password']);
    $email_status = $password_status = 1;

    //validation
    if (empty($email)) {
        $email_error  = "Email is required";
        $email_status = "";
    }
    if (empty($password)) {
        $password_error = "password is required";
        $password_status = "";
    }

    //Fetching user data
    if (!empty($email_status) && !empty($password_status)) {

        //fetching user having the input email
        $query = "SELECT * FROM users WHERE email= ?";
        $obj->Normal_Query($query, array($email));

        //if it does not exist
        if ($obj->Count_Rows() == 0) {
            $email_error = "Please enter correct email";
        } else {
            //if it exists
            $row            =   $obj->single_result(); //retrieves only single result and returns objects
            $user_id        =   $row->id;
            $user_email     =   $row->email;
            $user_password  =   $row->password;
            $user_name      =   $row->name;
            $user_image     =   $row->image;
            $clean_status   =   $row->clean_status;


            //if input password does not match
            if (!password_verify($password, $user_password)) {
                $password_error = "Please Enter correct Password";
            } else {
                //if input password matches with the registerd email, creating sessions and Updating user status to 1 
                $status = 1;
                $query = "UPDATE users SET status=? WHERE id=?";
                $obj->Normal_Query($query, [$status, $user_id]);
                
                if($clean_status == 0){ //if clean_status == 0, it will get the last id of the messages table

                    /** == Query for the last id==**/
                    $query = "SELECT msg_id FROM messages ORDER BY msg_id DESC LIMIT 1";
                    if($obj->Normal_Query($query)){
                        $last_row = $obj->single_result();
                        $last_msg_id = $last_row->msg_id +1;


                        /** == Inserting the last message id and the user id associated to clean table==**/
                        $query = "INSERT INTO clean(clean_message_id, clean_user_id) VALUES (?,?)";
                        if ($obj->Normal_Query($query, [$last_msg_id, $user_id])) {
                            
                            $update_clean_status = 1;

                            /**== updating user table with clean status to 1 ==**/
                            $query = "UPDATE users SET clean_status=? WHERE id=?";
                            $obj->Normal_Query($query, [$update_clean_status, $user_id]);

                            
                            
                            /**== User online status ==*/
                            $login_time = time();
                            $query = "SELECT * FROM users_activities WHERE user_id=?";
                            if($obj->Normal_Query($query, [$user_id])){

                                $row = $obj->single_result();
                                if($row == 0){
                                    $query = "INSERT INTO users_activities (user_id, login_time) VALUES(?,?)";
                                    $obj->Normal_Query($query, [$user_id, $login_time]);

                                    /**==Creating Session==**/
                                    $obj->create_session("user_name", $user_name);
                                    $obj->create_session('user_id', $user_id);
                                    $obj->create_session('user_image', $user_image);
                                    $obj->create_session('loader', 1);

                                    header("location:index.php"); //redirecting to home page

                                }else{
                                    
                                    $query = "UPDATE users_activities SET login_time=? WHERE user_id=?";
                                    $obj->Normal_Query($query, [$login_time, $user_id]);

                                    /**==Creating Session==**/
                                    $obj->create_session("user_name", $user_name);
                                    $obj->create_session('user_id', $user_id);
                                    $obj->create_session('user_image', $user_image);
                                    $obj->create_session('loader', 1);

                                    header("location:index.php"); //redirecting to home page
                                }
                            } /**== end User online status ==*/                           
                        }

                    };

                }else{

                    /**== User online status ==*/
                    $login_time = time();
                    $query = "SELECT * FROM users_activities WHERE user_id=?";
                    if ($obj->Normal_Query($query, [$user_id])) {

                        $row = $obj->single_result();
                        if ($row == 0) {
                            $query = "INSERT INTO users_activities (user_id, login_time) VALUES(?,?)";
                            $obj->Normal_Query($query, [$user_id, $login_time]);

                            /**==Creating Session==**/
                            $obj->create_session("user_name", $user_name);
                            $obj->create_session('user_id', $user_id);
                            $obj->create_session('user_image', $user_image);
                            $obj->create_session('loader', 1);

                            header("location:index.php"); //redirecting to home page

                        } else {

                            $query = "UPDATE users_activities SET login_time=? WHERE user_id=?";
                            $obj->Normal_Query($query, [$login_time, $user_id]);

                            /**==Creating Session==**/
                            $obj->create_session("user_name", $user_name);
                            $obj->create_session('user_id', $user_id);
                            $obj->create_session('user_image', $user_image);
                            $obj->create_session('loader', 1);

                            header("location:index.php"); //redirecting to home page
                        }
                    }
                    // /**== end User online status ==*/ 
                    // /**==if clean_status is not zero that means it  has already set up the clean table , 
                    // now only create the sessions ==**/
                    // $obj->create_session("user_name", $user_name);
                    // $obj->create_session('user_id', $user_id);
                    // $obj->create_session('user_image', $user_image);

                    // header("location:index.php"); //redirecting to home page
                }
                

            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up/Login</title>
    <?php include "components/css.php" ?>
</head>

<body>

    <?php if (isset($_SESSION['security'])) : ?>
        <div class="flash error-flash">
            <span class="remove">&times</span>
            <div class="flash-heading">
                <h3><span class="cross">&#x2715</span> Error!</h3>
            </div>
            <div class="flash-body">
                <p><?php echo $_SESSION['security']; ?></p>
            </div>
        </div>
    <?php endif; ?>
    <?php unset($_SESSION['security']); ?>
    <div class="signup-container">
        <div class="account-left">
            <div class="account-text">
                <h1>Let's Chat</h1>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Corporis harum, ullam cum minima eaque, nihil aliquid, aspernatur consequatur beatae neque dolores laudantium consequuntur mollitia!</p>
            </div> <!-- close account-text -->
        </div>
        <!--close accoont left-->

        <div class="account-right">
            <div class="form-area">

                <!-- Flash message after registration-->
                <?php if (isset($_SESSION['account_success'])) : ?>
                    <div class="alert alert-success">
                        <?php echo $_SESSION['account_success']; ?>
                    </div>
                    <!--close alert-->
                <?php endif; ?>
                <?php unset($_SESSION['account_success']) ?>

                <!--Login Form -->
                <form action="" method="POST">
                    <div class="group">
                        <h2 class="form-heading">User Login</h2>
                    </div> <!-- close group-->
                    <div class="group">
                        <input type="email" name="email" value="<?php if (isset($email)) : echo $email;
                                                                endif; ?>" class="control" placeholder="Enter your email">
                        <div class="error email-error">
                            <?php if (isset($email_error)) : ?>

                                <?php echo $email_error; ?>

                            <?php endif; ?>
                        </div>
                    </div> <!-- close group-->
                    <div class="group">
                        <input type="password" name="password" value="<?php if (isset($password)) : echo $password;
                                                                        endif; ?>" class="control" placeholder="Enter password">
                        <div class="error password-error">
                            <?php if (isset($password_error)) : ?>

                                <?php echo $password_error; ?>

                            <?php endif; ?>
                        </div>
                    </div> <!-- close group-->
                    <div class="group">
                        <input type="submit" name="login" class="btn signup-btn" value="User Login">
                    </div> <!-- close group-->
                    <div class="group">
                        <a href="signup.php" class="link">Not registered? Sign up here! </a>
                    </div>

                </form>
                <!--close form-->
            </div>
            <!--close form area -->
        </div>
        <!--close accoont right-->
    </div> <!-- signup-container close -->


    <script type="text/javascript" src="assets/js/jquery.min.js"></script>
    <script type="text/javascript" src="assets/js/file_lable.js"></script>
    <script type="text/javascript" src="assets/js/remove.js"></script>
</body>

</html>