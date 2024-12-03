<?php
require 'dbconnect.php';
session_start();
$errors = [];
if (isset($_SESSION['errors'])) {
    $errors = $_SESSION['errors'];
}

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    $stmt = $pdo->prepare('SELECT balance FROM accountbalance WHERE user_id = :id');
    $stmt->execute([':id' => $user[':id']]);
    $balance = $stmt->fetchColumn();
    $stmt = $pdo->prepare('SELECT accountPIN FROM users WHERE id = :id');
    $stmt->execute([':id' => $user[':id']]);
    $actualPIN = $stmt->fetchColumn();
} else { //Session user is not set yet, user isn't logged in
    header('Location: pages/login.php');
}

//If form == add_value
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_value'])) {
    $amount = $_POST['amount'];
    $accountPIN = $_POST['accountPIN'];
    // Check if amount and accountis a numeric value
    if (!is_numeric($amount) || !is_numeric($accountPIN)) {
        $errors['amount_type'] = 'Invalid amount format';
    }
    if (($amount <= 0)) {
        $errors['amount_sign'] = 'Number must be above 0';
    }
    if (!password_verify($accountPIN, $actualPIN)) {
        $errors['accountPIN'] = 'Account PIN does not match';
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: ../pages/addCash.php');
        exit();
    }
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare('UPDATE accountbalance 
                                   JOIN users ON accountbalance.user_id = users.id
                                   SET accountbalance.balance = accountbalance.balance + :amount
                                   WHERE accountbalance.user_id = :user_id');
         $stmt->execute([
            ':user_id' => $user[':id'],
            ':amount' => $amount
        ]);
        $pdo->commit();
        $_SESSION['message'] = "Operation was successful! You have added $" .number_format($amount,2) . " to your account successfully!";
        $_SESSION['message_type'] = "success";
        header('Location: ../pages/home.php');
    } catch (Exception $e) {
        // Rollback the transaction if there was an error
        $pdo->rollBack();
        $errors['transaction'] = 'Transaction has failed, something went wrong?';
        header('Location: /project/pages/sendCash.php');
        exit();
    }

}

//If form == transfer_out
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['transfer_out'])) {
    $amount = $_POST['amount'];
    $balance = (float) $balance;
    $accountPIN = $_POST['accountPIN'];

    // Check if amount is a numeric value
    if (!is_numeric($amount) || !is_numeric($accountPIN)) {
        $errors['number_format'] = "Input is not a number!";
    }
    // Convert to a decimal
    $amount = (float) $amount;

    if ($balance < $amount) {
        $errors['insufficient_balance'] = "User does not have enough in balance!";
    } else if (($amount <= 0)) {
        $errors['amount_sign'] = 'Number must be above 0';
    } else if (!password_verify($accountPIN, $actualPIN)) {
        $errors['accountPIN'] = 'Account PIN does not match';
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: ../pages/transferOut.php');
        exit();
    }
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare('UPDATE accountbalance 
                                   JOIN users ON accountbalance.user_id = users.id
                                   SET accountbalance.balance = accountbalance.balance - :amount
                                   WHERE accountbalance.user_id = :user_id');
        $stmt->execute([
         ':amount' => $amount,
            ':user_id' => $user[':id']
        ]);
        $pdo->commit();
        $_SESSION['message'] = "Operation was successful! You have transfered $" .number_format($amount,2) . " out of your account successfully!";
        $_SESSION['message_type'] = "success";
        header('Location: ../pages/home.php');
    } catch (Exception $e) {
        // Rollback the transaction if there was an error
        $pdo->rollBack();
        $errors['transaction'] = 'Transaction has failed, something went wrong?';
        header('Location: /project/transferOut.php');
        exit();
    }


}

//If form == send_money
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_money'])) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    //$balance = $user[':balance']['balance'];
    $amount = $_POST['amount'];
    $accountPIN = $_POST['accountPIN'];
    // Check if amount and accountPIN are numeric values
    if (!is_numeric($amount) || !is_numeric($accountPIN)) {
        $errors['amount_type'] = 'Invalid amount format';
    }
    $amount = (float) $amount;

    //Check if recipient email exists
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email =:email');
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch() == false) { //If email isn't already in users
        $errors['user_dne'] = "User does not exist in our database!";
    } else if ($balance < $amount) {
        $errors['insufficient_balance'] = "User does not have enough in balance!";
    } else if ($amount <= 0) {
        $errors['amount_sign'] = "Amount must be above zero.";
    } else if (!password_verify($accountPIN, $actualPIN)) {
        $errors['accountPIN'] = 'Account PIN does not match';
    }
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: ../pages/sendCash.php');
        exit();
    }
    try {
        $pdo->beginTransaction();

        //Send amount over to the recipient account
        $stmt = $pdo->prepare('UPDATE accountbalance 
                                   JOIN users ON accountbalance.user_id = users.id
                                   SET accountbalance.balance = accountbalance.balance + :amount
                                   WHERE users.email = :email');
        $stmt->execute([
            ':email' => $email,
            ':amount' => $amount
        ]);
        //Remove money from the sender's account
        $stmt = $pdo->prepare('UPDATE accountbalance 
                                   JOIN users ON accountbalance.user_id = users.id
                                   SET accountbalance.balance = accountbalance.balance - :amount
                                   WHERE accountbalance.user_id = :user_id');
        $stmt->execute([
            ':user_id' => $user[':id'],
            ':amount' => $amount
        ]);

        $pdo->commit();
        $_SESSION['message'] = "Operation was successful! You have sent $" .number_format($amount,2) . " to " . htmlspecialchars($email) . " successfully!";
        $_SESSION['message_type'] = "success";

    } catch (Exception $e) {
        // Rollback the transaction if there was an error
        $pdo->rollBack();
        $errors['transaction'] = 'Transaction has failed, something went wrong?';
        header('Location: /project/pages/sendCash.php');
        exit();
    }
    header('Location: /project/pages/home.php');
    exit();

}

//If form == addCard
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_card'])) {
    $creditNumber = $_POST['creditNumber'];
    $creditExpiry = $_POST['creditExpiry'];
    $stringFormatted = strtotime($creditExpiry);
    $stringDBbFormatted = date('Y-m-d H:i:s', $stringFormatted);
    $cvv = $_POST['cvv'];
    $inputDateObj = DateTime::createFromFormat('Y/m/01', $creditExpiry);
    
    if (strlen($creditNumber) < 15) {
        $errors['credit_length'] = "Credit number must be at least 15 characters long.";
    }

    if (!is_numeric($creditNumber)) {
        $errors['credit_error'] = "Credit number must be numeric.";
    }
    if (!is_numeric($cvv) || (strlen($cvv) < 3)) {
        $errors['cvv_error'] = "CVV error";
    }

    if (!luhn_check($creditNumber)) {
        $errors['credit_error'] = 'Invalid Credit Number';
    }
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: ../pages/addCard.php');
        exit();
    }
    $hashedCredit = password_hash($creditNumber, PASSWORD_BCRYPT);
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare('UPDATE accountbalance
                                    SET creditNumber = :creditNumber, creditExpiry = :creditExpiry
                                WHERE user_id = :userId;');
         $stmt->execute([
             ':creditNumber' => $hashedCredit,
             ':creditExpiry' => $stringDBbFormatted,
             ':userId' => $user[':id']
         ]);
        $pdo->commit();
        $_SESSION['message'] = "Operation was successful! You have added a card to your account!";
        $_SESSION['message_type'] = "success";
     } catch (Exception $e) {
        // Rollback the transaction if there was an error
        $pdo->rollBack();
        $errors['transaction'] = 'Transaction has failed, something went wrong?';
        header('Location: /project/pages/sendCash.php');
        exit();
    }
    header('Location: /project/pages/home.php');
    exit();
    
    


}
function luhn_check($number)
{
    // Strip any non-digits (useful for credit card numbers with spaces and hyphens)
    $number = preg_replace('/\D/', '', $number);

    // Set the string length and parity
    $number_length = strlen($number);
    $parity = $number_length % 2;

    // Loop through each digit and do the maths
    $total = 0;
    for ($i = 0; $i < $number_length; $i++) {
        $digit = $number[$i];
        // Multiply alternate digits by two
        if ($i % 2 == $parity) {
            $digit *= 2;
            // If the sum is two digits, add them together (in effect)
            if ($digit > 9) {
                $digit -= 9;
            }
        }
        // Total up the digits
        $total += $digit;
    }

    // If the total mod 10 equals 0, the number is valid
    return ($total % 10 == 0) ? TRUE : FALSE;

}
function validatePaymentDetails($creditNumber, $cvv)
{
    if (strlen($creditNumber) < 15) {
        $errors['credit_length'] = "Credit number must be at least 16 characters long.";
    }

    if (!is_numeric($creditNumber)) {
        $errors['credit_error'] = "Credit number must be numeric.";
    }
    if (!is_numeric($cvv) && (strlen($cvv) < 4)) {
        $errors['cvv_error'] = "CVV error";
    }

    if (!luhn_check($creditNumber)) {
        $errors['credit_error'] = 'Invalid Credit Number';
    }
    return true;
}