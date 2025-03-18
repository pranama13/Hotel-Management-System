<?php
include '../config.php';

$id = $_GET['id'];

$sql = "SELECT * FROM roombook WHERE id = '$id'";
$re = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_array($re)) {
    $Name = $row['Name'];
    $Email = $row['Email'];
    $Country = $row['Country'];
    $Phone = $row['Phone'];
    $RoomType = $row['RoomType'];
    $Bed = $row['Bed'];
    $NoofRoom = $row['NoofRoom'];
    $Meal = $row['Meal'];
    $cin = $row['cin'];
    $cout = $row['cout'];
    $noofday = $row['nodays'];
    $stat = $row['stat'];
    $roomtotal = $row['roomtotal'];
    $bedtotal = $row['bedtotal'];
    $mealtotal = $row['mealtotal'];
    $finaltotal = $row['finaltotal'];
}

if ($stat == "NotConfirm") {
    $st = "Confirm";
    $sql = "UPDATE roombook SET stat = '$st' WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        // Check if payment already exists
        $check_payment = mysqli_query($conn, "SELECT * FROM payment WHERE id='$id'");
        if (mysqli_num_rows($check_payment) == 0) {
            $psql = "INSERT INTO payment(id, Name, Email, RoomType, Bed, NoofRoom, cin, cout, noofdays, roomtotal, bedtotal, meal, mealtotal, finaltotal) 
                     VALUES ('$id', '$Name', '$Email', '$RoomType', '$Bed', '$NoofRoom', '$cin', '$cout', '$noofday', '$roomtotal', '$bedtotal', '$Meal', '$mealtotal', '$finaltotal')";
            mysqli_query($conn, $psql);
        }
        header("Location: roombook.php");
    }
}
?>