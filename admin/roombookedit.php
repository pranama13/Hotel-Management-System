<?php
include '../config.php';

// Fetch room data
$id = $_GET['id'];

$sql = "SELECT * FROM roombook WHERE id = '$id'";
$re = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_array($re)) {
    $Name = $row['Name'];
    $Email = $row['Email'];
    $Country = $row['Country'];
    $Phone = $row['Phone'];
    $cin = $row['cin'];
    $cout = $row['cout'];
    $noofday = $row['nodays'];
    $stat = $row['stat'];
}

if (isset($_POST['guestdetailedit'])) {
    $EditName = $_POST['Name'];
    $EditEmail = $_POST['Email'];
    $EditCountry = $_POST['Country'];
    $EditPhone = $_POST['Phone'];
    $EditRoomType = $_POST['RoomType'];
    $EditBed = $_POST['Bed'];
    $EditNoofRoom = $_POST['NoofRoom'];
    $EditMeal = $_POST['Meal'];
    $Editcin = $_POST['cin'];
    $Editcout = $_POST['cout'];

    $sql = "UPDATE roombook SET Name='$EditName', Email='$EditEmail', Country='$EditCountry', Phone='$EditPhone', RoomType='$EditRoomType', Bed='$EditBed', NoofRoom='$EditNoofRoom', Meal='$EditMeal', cin='$Editcin', cout='$Editcout', nodays=datediff('$Editcout','$Editcin') WHERE id='$id'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        // Recalculate pricing
        $type_of_room = 0;
        if ($EditRoomType == "Superior Room") $type_of_room = 3000;
        else if ($EditRoomType == "Deluxe Room") $type_of_room = 2000;
        else if ($EditRoomType == "Guest House") $type_of_room = 1500;
        else if ($EditRoomType == "Single Room") $type_of_room = 1000;

        $type_of_bed = $type_of_room * (int)$EditBed / 100;
        $type_of_meal = $type_of_bed * (int)$EditMeal;

        $Editnoofday = mysqli_fetch_array(mysqli_query($conn, "SELECT nodays FROM roombook WHERE id='$id'"))['nodays'];
        $editttot = $type_of_room * $Editnoofday * $EditNoofRoom;
        $editbtot = $type_of_bed * $Editnoofday * $EditNoofRoom;
        $editmepr = $type_of_meal * $Editnoofday * $EditNoofRoom;
        $editfintot = $editttot + $editbtot + $editmepr;

        // Update roombook with new totals
        $update_sql = "UPDATE roombook SET roomtotal='$editttot', bedtotal='$editbtot', mealtotal='$editmepr', finaltotal='$editfintot' WHERE id='$id'";
        mysqli_query($conn, $update_sql);

        // Update payment table only if it exists
        $check_payment = mysqli_query($conn, "SELECT * FROM payment WHERE id='$id'");
        if (mysqli_num_rows($check_payment) > 0) {
            $psql = "UPDATE payment SET Name='$EditName', Email='$EditEmail', RoomType='$EditRoomType', Bed='$EditBed', NoofRoom='$EditNoofRoom', Meal='$EditMeal', cin='$Editcin', cout='$Editcout', noofdays='$Editnoofday', roomtotal='$editttot', bedtotal='$editbtot', mealtotal='$editmepr', finaltotal='$editfintot' WHERE id='$id'";
            mysqli_query($conn, $psql);
        }

        header("Location: roombook.php");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <link rel="stylesheet" href="./css/roombook.css">
    <style>
        #editpanel { position: fixed; z-index: 1000; height: 100%; width: 100%; display: flex; justify-content: center; background-color: #00000079; }
        #editpanel .guestdetailpanelform { height: 620px; width: 1170px; background-color: #ccdff4; border-radius: 10px; position: relative; top: 20px; animation: guestinfoform .3s ease; }
    </style>
    <title>Edit Reservation</title>
</head>
<body>
    <div id="editpanel">
        <form method="POST" class="guestdetailpanelform">
            <div class="head">
                <h3>EDIT RESERVATION</h3>
                <a href="./roombook.php"><i class="fa-solid fa-circle-xmark"></i></a>
            </div>
            <div class="middle">
                <div class="guestinfo">
                    <h4>Guest information</h4>
                    <input type="text" name="Name" placeholder="Enter Full name" value="<?php echo $Name ?>">
                    <input type="email" name="Email" placeholder="Enter Email" value="<?php echo $Email ?>">
                    <?php
                    $countries = array("Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe");
                    ?>
                    <select name="Country" class="selectinput">
                        <option value selected>Select your country</option>
                        <?php
                            foreach($countries as $key => $value):
                            echo '<option value="'.$value.'">'.$value.'</option>';
                            endforeach;
                        ?>
                    </select>
                    <input type="text" name="Phone" placeholder="Enter Phoneno" value="<?php echo $Phone ?>">
                </div>
                <div class="line"></div>
                <div class="reservationinfo">
                    <h4>Reservation information</h4>
                    <select name="RoomType" class="selectinput">
                        <option value selected>Type Of Room</option>
                        <option value="Superior Room">SUPERIOR ROOM</option>
                        <option value="Deluxe Room">DELUXE ROOM</option>
                        <option value="Guest House">GUEST HOUSE</option>
                        <option value="Single Room">SINGLE ROOM</option>
                    </select>
                    <select name="Bed" class="selectinput">
                        <option value selected>Bedding Type</option>
                        <option value="Single">Single</option>
                        <option value="Double">Double</option>
                        <option value="Triple">Triple</option>
                        <option value="Quad">Quad</option>
                        <option value="None">None</option>
                    </select>
                    <select name="NoofRoom" class="selectinput">
                        <option value selected>No of Room</option>
                        <option value="1">1</option>
                    </select>
                    <select name="Meal" class="selectinput">
                        <option value selected>Meal</option>
                        <option value="Room only">Room only</option>
                        <option value="Breakfast">Breakfast</option>
                        <option value="Half Board">Half Board</option>
                        <option value="Full Board">Full Board</option>
                    </select>
                    <div class="datesection">
                        <span>
                            <label for="cin">Check-In</label>
                            <input name="cin" type="date" value="<?php echo $cin ?>">
                        </span>
                        <span>
                            <label for="cout">Check-Out</label>
                            <input name="cout" type="date" value="<?php echo $cout ?>">
                        </span>
                    </div>
                </div>
            </div>
            <div class="footer">
                <button class="btn btn-success" name="guestdetailedit">Edit</button>
            </div>
        </form>
    </div>
</body>
</html>