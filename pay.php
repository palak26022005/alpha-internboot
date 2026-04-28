<?php
// PayU Live Credentials
$MERCHANT_KEY = "5bKIVb";   
$SALT         = "z3sTGhhphpuyA4BaYIfkDghVsimpbXTm";  

// Student form data receive karo (GET/POST dono handle karo)
$firstname = $_GET['firstname'] ?? ($_POST['first_name'] ?? '');
$email     = $_GET['email'] ?? ($_POST['email'] ?? '');
$phone     = $_GET['mobile'] ?? ($_POST['mobile'] ?? '');

// Amount set karo (production ch DB/form se lena hovega)
$amount = 8850; // test ke liye

// Transaction ID generate karo
$txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);

// Product info
$productinfo = "Internship Registration";

// Success & Failure URLs

$surl = "https://alpha.internboot.com/success.php"; 
$furl = "https://alpha.internboot.com/failure.php";
// ✅ Hash generate karna (PayU requirement)
$hashString = $MERCHANT_KEY . '|' . $txnid . '|' . $amount . '|' . $productinfo . '|' 
             . $firstname . '|' . $email 
             . '|' . '' . '|' . '' . '|' . '' . '|' . '' . '|' . ''   // udf1–udf5 empty
             . '||||||' . $SALT;  // udf6–udf10 ke liye 6 pipes

$hash = strtolower(hash('sha512', $hashString));

// ✅ PayU Live URL
$PAYU_BASE_URL = "https://secure.payu.in/_payment";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>PayU Payment</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- ✅ External CSS file linked -->
  <link rel="stylesheet" href="pay.css">
</head>
<body>
  <div class="payment-container">
    <h2>💳 Internship Payment</h2>
    <p>Amount: ₹<?php echo $amount; ?> <br> Name: <?php echo htmlspecialchars($firstname); ?></p>
    <form action="<?php echo $PAYU_BASE_URL; ?>" method="post" name="payuForm">
        <input type="hidden" name="key" value="<?php echo $MERCHANT_KEY; ?>" />
        <input type="hidden" name="hash" value="<?php echo $hash; ?>" />
        <input type="hidden" name="txnid" value="<?php echo $txnid; ?>" />
        <input type="hidden" name="amount" value="<?php echo $amount; ?>" />
        <input type="hidden" name="firstname" value="<?php echo htmlspecialchars($firstname); ?>" />
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>" />
        <input type="hidden" name="phone" value="<?php echo htmlspecialchars($phone); ?>" />
        <input type="hidden" name="productinfo" value="<?php echo $productinfo; ?>" />
        <input type="hidden" name="surl" value="<?php echo $surl; ?>" />
        <input type="hidden" name="furl" value="<?php echo $furl; ?>" />

        <!-- ✅ Optional: extra student data via udf fields -->
        <!-- <input type="hidden" name="udf1" value="<?php echo $student_id; ?>" /> -->

        <button type="submit" class="pay-btn">Pay Now</button>
    </form>
  </div>
</body>
</html>