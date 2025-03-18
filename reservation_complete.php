<?php
session_start();
include '../config.php';

// Directly show success message without updating the database
echo "
<html>
<head>
<script src='https://unpkg.com/sweetalert/dist/sweetalert.min.js'></script>
</head>
<body>
<script>
swal({
    title: 'Reservation Successful',
    text: 'Your reservation has been confirmed. Redirecting to home...',
    icon: 'success',
    timer: 3000,
    buttons: false,
}).then(() => {
    window.location = 'home.php';
});
</script>
</body>
</html>
";
?>