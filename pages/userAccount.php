<?php
require_once 'dbconnect.php';

session_start();
$errors=[]; //Store errors that occur during login or registration

//Determines what type of form was sent using name of input, in this case signup
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])){
    $email=filter_input(INPUT_POST,'email',FILTER_SANITIZE_EMAIL); //Santizes email to ensure valid email format
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $password= $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    //If email format filter fails
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors['email'] = 'Invalid email format.';
    }
    if(empty($first_name) || empty($last_name)) { //If either name fields are left empty
        $errors['names'] = 'Name is required.';
    }
    if(strlen($password)<8){
        $errors['password'] = 'Password must be at least eight characters long.';
    }
    if($password !== $password_confirm){
        $errors['password_confirm'] = 'Passwords do not match.';
        
    }
    $stmt = $pdo ->prepare('SELECT * FROM users WHERE email =:email');
    $stmt -> execute(['email'=> $email]);

    if($stmt->fetch()){ //If email is already in users
        $errors['user_exist']='Email is already registered. Please try to log in.';
    }
    if(!empty($errors)){ //If there are any errors, stop script and reload page
        $_SESSION['errors']=$errors;
        header('Location: ./pages/register.php');
        exit();
    }
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $pdo -> prepare('INSERT INTO users (email, password, firstName, lastName) 
                            VALUES(:email,:password,:first_name,:last_name)');
                            
                            
    var_dump($email, $hashedPassword, $first_name, $last_name);

    //Execute actual statement
    $stmt -> execute([
        ':email' => $email,
        ':password' => $hashedPassword,
        ':first_name' => $first_name,
        ':last_name' => $last_name
    ]);
    
    header('Location: ./pages/login.php'); //Send to login.php
    exit();
}

if($_SERVER['REQUEST_METHOD']== 'POST' && isset($_POST['login'])){
    $email = filter_input(INPUT_POST,'email',FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors['email'] = 'Invalid email format';
    }
    if(empty($password)){
        $errors['password'] = 'Password cannot be empty';
    }
    if(!empty($errors)){
        $_SESSION['errors']=$errors;
        header('Location: login.php');
        exit();
    }
    $stmt = $pdo -> prepare("SELECT * FROM users WHERE email=:email");
    $stmt -> execute(['email'=> $email]);
    $user = $stmt->fetch();

    if($user && password_verify($password, $user['password'])){
        $_SESSION['user'] = [
            ':id'=> $user['id'],
            ':email'=> $user['email'],
            ':first_name'=> $user['firstName'],
            ':last_name'=> $user['lastName'],
        ];
        header('Location: ./pages/home.php');
        exit();
    }else{
        $errors['login']='Invalid email or password';
        $_SESSION['errors']=$errors;
        header('Location: index.html');
        exit();
    }

}