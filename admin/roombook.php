<?php
session_start();
include '../config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Sweet Alert -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <link rel="stylesheet" href="./css/roombook.css">
    <title>Holiday Inn - Admin</title>
</head>

<body>
    <!-- Guest Detail Panel -->
    <div id="guestdetailpanel">
    <form action="roombook.php" method="POST" class="guestdetailpanelform">
            <div class="head">
                <h3>RESERVATION</h3>
                <i class="fa-solid fa-circle-xmark" onclick="adduserclose()"></i>
            </div>
            <div class="middle">
                <div class="guestinfo">
                    <h4>Guest information</h4>
                    <input type="text" name="Name" placeholder="Enter Full name" required>
                    <input type="email" name="Email" placeholder="Enter Email" required>
                    <input type="text" name="Phone" placeholder="Enter Phoneno" required>
                </div>

                <div class="line"></div>

                <div class="reservationinfo">
                    <h4>Booking information</h4>
                    <select name="RoomType" class="selectinput" onchange="updatePrice()" id="roomType" required>
                        <option value="" data-price="0">Type Of Room</option>
                        <option value="Superior Room" data-price="3000">SUPERIOR ROOM - $3000/night</option>
                        <option value="Deluxe Room" data-price="2000">DELUXE ROOM - $2000/night</option>
                        <option value="Guest House" data-price="1500">GUEST HOUSE - $1500/night</option>
                        <option value="Single Room" data-price="1000">SINGLE ROOM - $1000/night</option>
                    </select>
                    <select name="Bed" class="selectinput" onchange="updatePrice()" id="bedType" required>
                        <option value="" data-price="0">Bedding Type</option>
                        <option value="Single" data-price="1">Single</option>
                        <option value="Double" data-price="2">Double</option>
                        <option value="Triple" data-price="3">Triple</option>
                        <option value="Quad" data-price="4">Quad</option>
                        <option value="None" data-price="0">None</option>
                    </select>
                    <select name="NoofRoom" class="selectinput" onchange="updatePrice()" id="noOfRooms" required>
                        <option value="" selected>No of Room</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                    </select>
                    <select name="Meal" class="selectinput" onchange="updatePrice()" id="mealType" required>
                        <option value="" data-price="0">Meal</option>
                        <option value="Room only" data-price="0">Room only - $0</option>
                        <option value="Breakfast" data-price="2">Breakfast</option>
                        <option value="Half Board" data-price="3">Half Board</option>
                        <option value="Full Board" data-price="4">Full Board</option>
                    </select>
                    <div class="datesection">
                        <span>
                            <label for="cin">Check-In</label>
                            <input name="cin" type="date" id="checkin" onchange="updatePrice()" required>
                        </span>
                        <span>
                            <label for="cout">Check-Out</label>
                            <input name="cout" type="date" id="checkout" onchange="updatePrice()" required>
                        </span>
                    </div>
                    <!-- Price Breakdown Display -->
                    <div class="price-section mt-3">
                        <p>Room Total: $<span id="roomTotal">0</span></p>
                        <p>Bed Total: $<span id="bedTotal">0</span></p>
                        <p>Meal Total: $<span id="mealTotal">0</span></p>
                        <h4>Final Total: $<span id="finalTotal">0</span></h4>
                    </div>
                </div>
            </div>
            <div class="footer">
                <button class="btn btn-success" name="guestdetailsubmit" type="submit">Book Now</button>
            </div>
        </form>

        <!-- PHP Processing Logic -->
        <?php
if (isset($_POST['guestdetailsubmit'])) {
    $Name = $_POST['Name'];
    $Email = $_POST['Email'];
    $Phone = $_POST['Phone'];
    $RoomType = $_POST['RoomType'];
    $Bed = $_POST['Bed'];
    $NoofRoom = $_POST['NoofRoom'];
    $Meal = $_POST['Meal'];
    $cin = $_POST['cin'];
    $cout = $_POST['cout'];

    if ($Name == "" || $Email == "" || $Phone == "" || $RoomType == "" || $cin == "" || $cout == "") {
        echo "<script>swal({ title: 'Fill the proper details', icon: 'error' });</script>";
    } else {
        $roomPrices = [
            'Superior Room' => 3000,
            'Deluxe Room' => 2000,
            'Guest House' => 1500,
            'Single Room' => 1000
        ];

        $checkin = new DateTime($cin);
        $checkout = new DateTime($cout);
        $days = $checkout->diff($checkin)->days;

        $type_of_room = $roomPrices[$RoomType];
        $type_of_bed = $type_of_room * (int)$Bed / 100;
        $type_of_meal = $type_of_bed * (int)$Meal;

        $roomtotal = $type_of_room * $days * $NoofRoom;
        $bedtotal = $type_of_bed * $days * $NoofRoom;
        $mealtotal = $type_of_meal * $days * $NoofRoom;
        $finaltotal = $roomtotal + $bedtotal + $mealtotal;

        $sta = "NotConfirm";
        $sql = "INSERT INTO roombook(Name,Email,Phone,RoomType,Bed,NoofRoom,Meal,cin,cout,stat,nodays,roomtotal,bedtotal,mealtotal,finaltotal) 
                VALUES ('$Name','$Email','$Phone','$RoomType','$Bed','$NoofRoom','$Meal','$cin','$cout','$sta','$days','$roomtotal','$bedtotal','$mealtotal','$finaltotal')";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            $last_id = mysqli_insert_id($conn);
            echo "<script>
                swal({
                    title: 'Booking Recorded',
                    text: 'Final Total: $$finaltotal - Proceed to Payment?',
                    icon: 'info',
                    buttons: { cancel: 'Cancel', confirm: { text: 'Pay Now', value: true } }
                }).then((willPay) => {
                    if (willPay) {
                        console.log('Redirecting to: payment.php?id=$last_id&amount=$finaltotal');
                        window.location = 'payment.php?id=$last_id&amount=$finaltotal';
                    } else {
                        window.location = 'roombook.php';
                    }
                });
            </script>";
        } else {
            echo "<script>swal({ title: 'Something went wrong: " . addslashes(mysqli_error($conn)) . "', icon: 'error' });</script>";
        }
    }
}
?>
    </div>

    <!-- Room Availability Calculation -->
    <?php
    $rsql = "select * from room";
    $rre = mysqli_query($conn, $rsql);
    $r = 0;
    $sc = 0;
    $gh = 0;
    $sr = 0;
    $dr = 0;

    while ($rrow = mysqli_fetch_array($rre)) {
        $r = $r + 1;
        $s = $rrow['type'];
        if ($s == "Superior Room") {
            $sc = $sc + 1;
        }
        if ($s == "Guest House") {
            $gh = $gh + 1;
        }
        if ($s == "Single Room") {
            $sr = $sr + 1;
        }
        if ($s == "Deluxe Room") {
            $dr = $dr + 1;
        }
    }

    $csql = "select * from payment";
    $cre = mysqli_query($conn, $csql);
    $cr = 0;
    $csc = 0;
    $cgh = 0;
    $csr = 0;
    $cdr = 0;
    while ($crow = mysqli_fetch_array($cre)) {
        $cr = $cr + 1;
        $cs = $crow['RoomType'];
        if ($cs == "Superior Room") {
            $csc = $csc + 1;
        }
        if ($cs == "Guest House") {
            $cgh = $cgh + 1;
        }
        if ($cs == "Single Room") {
            $csr = $csr + 1;
        }
        if ($cs == "Deluxe Room") {
            $cdr = $cdr + 1;
        }
    }

    $f1 = $sc - $csc; // Superior Room
    if ($f1 <= 0) { $f1 = "NO"; }
    $f2 = $gh - $cgh; // Guest House
    if ($f2 <= 0) { $f2 = "NO"; }
    $f3 = $sr - $csr; // Single Room
    if ($f3 <= 0) { $f3 = "NO"; }
    $f4 = $dr - $cdr; // Deluxe Room
    if ($f4 <= 0) { $f4 = "NO"; }
    $f5 = $r - $cr; // Total available rooms
    if ($f5 <= 0) { $f5 = "NO"; }
    ?>

    <!-- Search Section -->
    <div class="searchsection">
        <input type="text" name="search_bar" id="search_bar" placeholder="search..." onkeyup="searchFun()">
        <button class="adduser" id="adduser" onclick="adduseropen()"><i class="fa-solid fa-bookmark"></i> Add</button>
       
    </div>

    <!-- Room Booking Table -->
    <div class="roombooktable" class="table-responsive-xl">
        <?php
            $roombooktablesql = "SELECT * FROM roombook";
            $roombookresult = mysqli_query($conn, $roombooktablesql);
            $nums = mysqli_num_rows($roombookresult);
        ?>
        <table class="table table-bordered" id="table-data">
            <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Country</th>
                    <th scope="col">Phone</th>
                    <th scope="col">Type of Room</th>
                    <th scope="col">Type of Bed</th>
                    <th scope="col">No of Room</th>
                    <th scope="col">Meal</th>
                    <th scope="col">Check-In</th>
                    <th scope="col">Check-Out</th>
                    <th scope="col">No of Day</th>
                    <th scope="col">Room Total</th>
                    <th scope="col">Bed Total</th>
                    <th scope="col">Meal Total</th>
                    <th scope="col">Final Total</th>
                    <th scope="col">Status</th>
                    <th scope="col" class="action">Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
            while ($res = mysqli_fetch_array($roombookresult)) {
            ?>
                <tr>
                    <td><?php echo $res['id'] ?></td>
                    <td><?php echo $res['Name'] ?></td>
                    <td><?php echo $res['Email'] ?></td>
                    <td><?php echo $res['Country'] ?></td>
                    <td><?php echo $res['Phone'] ?></td>
                    <td><?php echo $res['RoomType'] ?></td>
                    <td><?php echo $res['Bed'] ?></td>
                    <td><?php echo $res['NoofRoom'] ?></td>
                    <td><?php echo $res['Meal'] ?></td>
                    <td><?php echo $res['cin'] ?></td>
                    <td><?php echo $res['cout'] ?></td>
                    <td><?php echo $res['nodays'] ?></td>
                    <td><?php echo $res['roomtotal'] ?></td>
                    <td><?php echo $res['bedtotal'] ?></td>
                    <td><?php echo $res['mealtotal'] ?></td>
                    <td><?php echo $res['finaltotal'] ?></td>
                    <td><?php echo $res['stat'] ?></td>
                    <td class="action">
                        <?php
                            if ($res['stat'] == "Confirm") {
                                echo " ";
                            } else {
                                echo "<a href='roomconfirm.php?id=". $res['id'] ."'><button class='btn btn-success'>Confirm</button></a>";
                            }
                        ?>
                        <a href="roombookedit.php?id=<?php echo $res['id'] ?>"><button class="btn btn-primary">Edit</button></a>
                        <a href="roombookdelete.php?id=<?php echo $res['id'] ?>"><button class='btn btn-danger'>Delete</button></a>
                    </td>
                </tr>
            <?php
            }
            ?>
            </tbody>
        </table>
    </div>
</body>
<script src="./javascript/roombook.js"></script>
<script>
function updatePrice() {
    const roomType = document.getElementById('roomType');
    const bedType = document.getElementById('bedType');
    const noOfRooms = document.getElementById('noOfRooms');
    const mealType = document.getElementById('mealType');
    const checkin = document.getElementById('checkin');
    const checkout = document.getElementById('checkout');
    
    const roomTotalSpan = document.getElementById('roomTotal');
    const bedTotalSpan = document.getElementById('bedTotal');
    const mealTotalSpan = document.getElementById('mealTotal');
    const finalTotalSpan = document.getElementById('finalTotal');

    if (roomType.value && bedType.value && noOfRooms.value && mealType.value && checkin.value && checkout.value) {
        const roomPrice = parseFloat(roomType.options[roomType.selectedIndex].getAttribute('data-price'));
        const bedPercentage = parseFloat(bedType.options[bedType.selectedIndex].getAttribute('data-price'));
        const mealMultiplier = parseFloat(mealType.options[mealType.selectedIndex].getAttribute('data-price'));
        const rooms = parseInt(noOfRooms.value) || 1;
        
        const checkinDate = new Date(checkin.value);
        const checkoutDate = new Date(checkout.value);
        const days = Math.ceil((checkoutDate - checkinDate) / (1000 * 60 * 60 * 24));

        if (days > 0) {
            const roomTotal = roomPrice * days * rooms;
            const bedTotal = (roomPrice * bedPercentage / 100) * days * rooms;
            const mealTotal = ((roomPrice * bedPercentage / 100) * mealMultiplier) * days * rooms;
            const finalTotal = roomTotal + bedTotal + mealTotal;

            roomTotalSpan.textContent = roomTotal.toFixed(2);
            bedTotalSpan.textContent = bedTotal.toFixed(2);
            mealTotalSpan.textContent = mealTotal.toFixed(2);
            finalTotalSpan.textContent = finalTotal.toFixed(2);
        } else {
            roomTotalSpan.textContent = '0';
            bedTotalSpan.textContent = '0';
            mealTotalSpan.textContent = '0';
            finalTotalSpan.textContent = '0';
        }
    }
}
</script>
</html>