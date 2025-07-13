<?php
// Variable declaration
$username = "";
$email = "";
$errors = array();

// REGISTER USER
if (isset($_POST['reg_user'])) {
    // Receive all input values from the form
    $username = esc($_POST['username']);
    $email = esc($_POST['email']);
    $password_1 = esc($_POST['password_1']);
    $password_2 = esc($_POST['password_2']);

    // Form validation: ensure that the form is correctly filled
    if (empty($username)) {
        array_push($errors, "Uhmm...We're gonna need your username");
    }
    if (empty($email)) {
        array_push($errors, "Oops.. Email is missing");
    }
    if (empty($password_1)) {
        array_push($errors, "Uh-oh, you forgot the password");
    }
    if ($password_1 != $password_2) {
        array_push($errors, "The two passwords do not match");
    }

    // Ensure that no user is registered twice.
    // The email and usernames should be unique
    $user_check_query = "SELECT * FROM users WHERE username='$username' OR email='$email' LIMIT 1";

    $result = mysqli_query($conn, $user_check_query);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        // If the user exists
        if ($user['username'] === $username) {
            array_push($errors, "Username already exists");
        }
        if ($user['email'] === $email) {
            array_push($errors, "Email already exists");
        }
    }
    // Register the user if there are no errors in the form
    if (count($errors) == 0) {
        $password = md5($password_1); // Encrypt the password before saving it in the database
        $query = "INSERT INTO users (username, email, password, created_at, updated_at) VALUES('$username', '$email', '$password', now(), now())";
        mysqli_query($conn, $query);

        // Get the ID of the created user
        $reg_user_id = mysqli_insert_id($conn);

        // Put the logged-in user into the session array
        $_SESSION['user'] = getUserById($reg_user_id);

        // If the user is an admin, redirect to the admin area
        if (in_array($_SESSION['user']['role'], ["Admin", "Author"])) {
            $_SESSION['message'] = "You are now logged in";
            // Redirect to the admin area
            header('location: ' . BASE_URL . '/admin/dashboard.php');
            exit(0);
        } else {
            $_SESSION['message'] = "You are now logged in";
            // Redirect to the public area
            header('location: index.php');
            exit(0);
        }
    }
}

// LOG USER IN
if (isset($_POST['login_btn'])) {
    $username = esc($_POST['username']);
    $password = esc($_POST['password']);

    if (empty($username)) {
        array_push($errors, "Username required");
    }
    if (empty($password)) {
        array_push($errors, "Password required");
    }
    if (empty($errors)) {
        $password = md5($password); // Encrypt the password
        $sql = "SELECT * FROM users WHERE username='$username' and password='$password' LIMIT 1";

        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            // Get the ID of the created user
            $reg_user_id = mysqli_fetch_assoc($result)['id'];

            // Put the logged-in user into the session array
            $_SESSION['user'] = getUserById($reg_user_id);

            // If the user is an admin, redirect to the admin area
            if (in_array($_SESSION['user']['role'], ["Admin", "Author"])) {
                $_SESSION['message'] = "You are now logged in";
                // Redirect to the admin area
                header('location: ' . BASE_URL . '/admin/dashboard.php');
                exit(0);
            } else {
                $_SESSION['message'] = "You are now logged in";
                // Redirect to the public area
                header('location: index.php');
                exit(0);
            }
        } else {
            array_push($errors, 'Wrong credentials');
        }
    }
}

// Escape value from the form
function esc($value)
{
    // Bring the global db connect object into the function
    global $conn;

    $val = trim($value); // Remove empty spaces surrounding the string
    $val = mysqli_real_escape_string($conn, $val);

    return $val;
}

// Get user info from user ID
function getUserById($id)
{
    global $conn;
    $sql = "SELECT * FROM users WHERE id=$id LIMIT 1";

    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    // Returns user in an array format:
    // ['id'=>1, 'username' => 'Awa', 'email'=>'a@a.com', 'password'=> 'mypass']
    return $user;
}
?>
