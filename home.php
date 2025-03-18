<?php
include 'config.php';
session_start();

// Check if the user is logged in
$usermail = $_SESSION['usermail'] ?? '';
if (empty($usermail)) {
    header("Location: index.php");
    exit;
}

// Process form submission before any output
if (isset($_POST['guestdetailsubmit'])) {
    $Name = $_POST['Name'];
    $Email = $_POST['Email'];
    $Country = $_POST['Country'];
    $Phone = $_POST['Phone'];
    $RoomType = $_POST['RoomType'];
    $Bed = $_POST['Bed'];
    $NoofRoom = $_POST['NoofRoom'];
    $Meal = $_POST['Meal'];
    $cin = $_POST['cin'];
    $cout = $_POST['cout'];

    // Validate required fields
    if (empty($Name) || empty($Email) || empty($Country) || empty($RoomType) || empty($cin) || empty($cout)) {
        $_SESSION['error'] = "Please fill in all required details.";
    } else {
        // Define room prices
        $roomPrices = [
            'Superior Room' => 3000,
            'Deluxe Room' => 2000,
            'Guest House' => 1500,
            'Single Room' => 1000
        ];

        // Calculate number of days
        $checkin = new DateTime($cin);
        $checkout = new DateTime($cout);
        $days = $checkout->diff($checkin)->days;

        // Calculate totals
        $type_of_room = $roomPrices[$RoomType];
        $type_of_bed = $type_of_room * (int)$Bed / 100;
        $type_of_meal = $type_of_bed * (int)$Meal;

        $roomtotal = $type_of_room * $days * $NoofRoom;
        $bedtotal = $type_of_bed * $days * $NoofRoom;
        $mealtotal = $type_of_meal * $days * $NoofRoom;
        $finaltotal = $roomtotal + $bedtotal + $mealtotal;

        // Insert booking into database
        $sta = "NotConfirm";
        $sql = "INSERT INTO roombook(Name,Email,Country,Phone,RoomType,Bed,NoofRoom,Meal,cin,cout,stat,nodays,roomtotal,bedtotal,mealtotal,finaltotal) 
                VALUES ('$Name','$Email','$Country','$Phone','$RoomType','$Bed','$NoofRoom','$Meal','$cin','$cout','$sta','$days','$roomtotal','$bedtotal','$mealtotal','$finaltotal')";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            $last_id = mysqli_insert_id($conn);
            header("Location: payment.php?id=$last_id&amount=$finaltotal");
            exit;
        } else {
            $_SESSION['error'] = "Something went wrong: " . mysqli_error($conn);
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
    <title>Hotel Holiday Inn - Luxury Accommodation</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Caveat:wght@400..700&family=Comic+Neue:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&family=Playwrite+IT+Moderna:wght@100..400&display=swap" rel="stylesheet">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <!-- Sweet Alert -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --text-color: #333;
            --bg-color: #f5f6fa;
            --card-bg: #ffffff;
            --shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        [data-theme="dark"] {
            --primary-color: #34495e;
            --secondary-color: #2980b9;
            --text-color: #ecf0f1;
            --bg-color: #1a1a1a;
            --card-bg: #2c2c2c;
            --shadow: 0 4px 6px rgba(255,255,255,0.1);
        }

        body {
            background: var(--bg-color);
            color: var(--text-color);
            font-family: 'Segoe UI', Arial, sans-serif;
            transition: all 0.3s ease;
            line-height: 1.6;
        }

        nav {
            background: var(--primary-color);
            padding: 1rem 2rem;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        nav .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        nav .logo img {
            width: 50px;
            border-radius: 50%;
            transition: transform 0.3s ease;
        }

        nav .logo img:hover {
            transform: scale(1.1);
        }

        nav ul {
            display: flex;
            align-items: center;
            gap: 2rem;
            margin: 0;
        }

        nav ul li {
            list-style: none;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        nav ul li a:hover {
            color: var(--secondary-color);
        }

        .theme-toggle {
            background: none;
            border: none;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0.5rem;
        }

        .carousel-image {
            height: 100vh;
            object-fit: cover;
            filter: brightness(70%);
            width: 100%;
        }

        .welcomeline {
            position: absolute;
display: flex;
height: 100vh;
width: 100%;
justify-content: center;
align-items: center;
z-index: 100;
font-family: "Caveat", cursive; /* Beautiful handwritten font */
font-optical-sizing: auto;
font-weight: 700; /* Bold for emphasis, replacing <weight> */
font-style: normal;
font-size: 2.5rem; /* Typical h1 size, adjustable */
color: #ff69b4; /* Hot pink color */
text-shadow: 0 0 10px rgba(255, 105, 180, 0.8), /* Pink glow effect */
            0 0 20px rgba(255, 105, 180, 0.6),
            0 0 30px rgba(255, 105, 180, 0.4); /* Multiple layers for depth */
letter-spacing: 1.5px; /* Slight spacing for elegance */
}

        .welcometag {
            color: white;
            font-size: 48px;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            position: absolute;
display: flex;
height: 100vh;
width: 100%;
justify-content: center;
align-items: center;
z-index: 100;
font-family: "Caveat", cursive; /* Beautiful handwritten font */
font-optical-sizing: auto;
font-weight: 700; /* Bold for emphasis, replacing <weight> */
font-style: normal;
font-size: 8rem; /* Typical h1 size, adjustable */
color:rgb(255, 81, 168); /* Hot pink color */
text-shadow: 0 0 5px #f893c6, /* Pink glow effect */
            0 0 20px rgb(253, 27, 185),
            0 0 30px rgb(211, 100, 255); /* Multiple layers for depth */
letter-spacing: 1.5px; /* Slight spacing for elegance */
        }

        #guestdetailpanel {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
        }

        #guestdetailpanel .guestdetailpanelform {
            background: var(--card-bg);
            border-radius: 15px;
            box-shadow: var(--shadow);
            padding: 2rem;
            width: 900px;
            max-height: 90vh;
            overflow-y: auto;
            animation: slideUp 0.5s ease;
        }

        .roombox {
            background: var(--card-bg);
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s ease;
            box-shadow: var(--shadow);
        }

        .roombox:hover {
            transform: translateY(-10px);
        }

        .hotelphoto {
            height: 200px;
            background-size: cover;
        }

        .h1 { background-image: url('./image/hotel1.jpg'); }
        .h2 { background-image: url('./image/hotel2.jpg'); }
        .h3 { background-image: url('./image/hotel3.jpg'); }
        .h4 { background-image: url('./image/hotel4.jpg'); }

        .roomdata {
            padding: 1rem;
        }

        .facility .box {
            background: var(--card-bg);
            padding: 1rem;
            border-radius: 10px;
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
            text-align: center;
            height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .facility .box:hover {
            background: var(--secondary-color);
            color: white;
            transform: scale(1.05);
        }

        .facility-img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 0.5rem;
        }

        .btn-primary {
            background: var(--secondary-color);
            border: none;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: var(--primary-color);
            transform: translateY(-2px);
        }

        .head {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 2rem;
        }

        footer {
            background: var(--primary-color);
            color: white;
            padding: 4rem 0 2rem;
            border-top: 4px solid var(--secondary-color);
        }

        footer .row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        footer .col-md-4 {
            flex: 0 0 33.333%;
            max-width: 33.333%;
            text-align: center;
            padding: 0 15px;
        }

        footer .footer-logo {
            max-width: 120px;
            margin: 0 auto 1rem;
            display: block;
            transition: transform 0.3s ease;
        }

        footer .footer-logo:hover {
            transform: scale(1.05);
        }

        footer h4 {
            color: var(--secondary-color);
            margin-bottom: 1.5rem;
            font-weight: 600;
            position: relative;
            padding-bottom: 0.5rem;
        }

        footer h4::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 2px;
            background: var(--secondary-color);
        }

        footer a {
            color: #ffffff;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        footer a:hover {
            color: var(--secondary-color);
        }

        footer .social a {
            font-size: 1.5rem;
            margin: 0 0.75rem;
            transition: transform 0.3s ease;
        }

        footer .social a:hover {
            transform: translateY(-3px);
        }

        footer .list-unstyled {
            padding: 0;
        }

        footer .list-unstyled li {
            margin-bottom: 0.75rem;
        }

        footer .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 1.5rem;
            margin-top: 2rem;
            text-align: center;
        }

        @keyframes slideUp {
            from {
                transform: translate(-50%, -40%);
                opacity: 0;
            }
            to {
                transform: translate(-50%, -50%);
                opacity: 1;
            }
        }

        @media (max-width: 768px) {
            nav {
                padding: 1rem;
            }

            nav ul {
                flex-wrap: wrap;
                gap: 1rem;
            }

            #guestdetailpanel .guestdetailpanelform {
                width: 90%;
                padding: 1rem;
            }

            .carousel-image {
                height: 70vh;
            }

            .welcometag {
                font-size: 32px;
            }

            footer {
                padding: 2rem 0 1rem;
            }

            footer .col-md-4 {
                flex: 0 0 100%;
                max-width: 100%;
                margin-bottom: 2rem;
            }
        }
    </style>
</head>

<body>
    <!-- Error messages -->
    <?php if (isset($_SESSION['error'])) { ?>
        <script>
            swal({
                title: "Error",
                text: "<?php echo $_SESSION['error']; ?>",
                icon: "error"
            });
        </script>
    <?php unset($_SESSION['error']); } ?>

    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="logo">
            <img class="bluebirdlogo" src="./image/logo.jpg" alt="Hotel Holiday Inn Logo">
            <p class="fw-bold text-white m-0">Holiday Inn</p>
        </div>
        <ul>
            <li><a href="#firstsection">Home</a></li>
            <li><a href="#secondsection">Rooms</a></li>
            <li><a href="#thirdsection">Facilities</a></li>
            <li><a href="#contactus">Contact</a></li>
            <li><button class="theme-toggle" id="themeToggle"><i class="fas fa-moon"></i></button></li>
            <li><a href="./logout.php"><button class="btn btn-danger">Logout</button></a></li>
        </ul>
    </nav>

    <!-- Carousel and Booking Form -->
    <section id="firstsection" class="carousel slide carousel_section" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="carousel-image" src="./image/hotel1.jpg" alt="Hotel Image 1">
            </div>
            <div class="carousel-item">
                <img class="carousel-image" src="./image/hotel2.jpg" alt="Hotel Image 2">
            </div>
            <div class="carousel-item">
                <img class="carousel-image" src="./image/hotel3.jpg" alt="Hotel Image 3">
            </div>
            <div class="carousel-item">
                <img class="carousel-image" src="./image/hotel4.jpg" alt="Hotel Image 4">
            </div>

            <div class="welcomeline">
                <h1 class="welcometag" >Welcome to Holiday Inn</h1>
            </div>

            <!-- Booking Form -->
            <div id="guestdetailpanel">
                <form action="" method="POST" class="guestdetailpanelform">
                    <div class="head d-flex justify-content-between align-items-center">
                        <h3>BOOKING</h3>
                        <i class="fa-solid fa-circle-xmark" onclick="closebox()" style="cursor: pointer;"></i>
                    </div>
                    <div class="middle">
                        <div class="guestinfo">
                            <h4>Guest Information</h4>
                            <input type="text" name="Name" placeholder="Enter Full Name" class="form-control mb-3" required>
                            <input type="email" name="Email" placeholder="Enter Email" class="form-control mb-3" required>
                            <select name="Country" class="form-control mb-3" required>
                                <option value="" selected>Select your country</option>
                                <?php
                                    $countries = array("Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe");
                                    foreach($countries as $value):
                                        echo '<option value="'.$value.'">'.$value.'</option>';
                                    endforeach;
                                ?>
                            </select>
                            <input type="text" name="Phone" placeholder="Enter Phone Number" class="form-control mb-3" required>
                        </div>

                        <hr>

                        <div class="reservationinfo">
                            <h4>Booking Information</h4>
                            <select name="RoomType" class="form-control mb-3" onchange="updatePrice()" id="roomType" required>
                                <option value="" data-price="0">Type Of Room</option>
                                <option value="Superior Room" data-price="3000">SUPERIOR ROOM - $3000/night</option>
                                <option value="Deluxe Room" data-price="2000">DELUXE ROOM - $2000/night</option>
                                <option value="Guest House" data-price="1500">GUEST HOUSE - $1500/night</option>
                                <option value="Single Room" data-price="1000">SINGLE ROOM - $1000/night</option>
                            </select>
                            <select name="Bed" class="form-control mb-3" onchange="updatePrice()" id="bedType" required>
                                <option value="" data-price="0">Bedding Type</option>
                                <option value="Single" data-price="1">Single</option>
                                <option value="Double" data-price="2">Double</option>
                                <option value="Triple" data-price="3">Triple</option>
                                <option value="Quad" data-price="4">Quad</option>
                                <option value="None" data-price="0">None</option>
                            </select>
                            <select name="NoofRoom" class="form-control mb-3" onchange="updatePrice()" id="noOfRooms" required>
                                <option value="" selected>No of Rooms</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                            <select name="Meal" class="form-control mb-3" onchange="updatePrice()" id="mealType" required>
                                <option value="" data-price="0">Meal</option>
                                <option value="Room only" data-price="0">Room only - $0</option>
                                <option value="Breakfast" data-price="2">Breakfast</option>
                                <option value="Half Board" data-price="3">Half Board</option>
                                <option value="Full Board" data-price="4">Full Board</option>
                            </select>
                            <div class="datesection d-flex gap-3">
                                <span class="flex-grow-1">
                                    <label for="cin">Check-In</label>
                                    <input name="cin" type="date" id="checkin" class="form-control" onchange="updatePrice()" required>
                                </span>
                                <span class="flex-grow-1">
                                    <label for="cout">Check-Out</label>
                                    <input name="cout" type="date" id="checkout" class="form-control" onchange="updatePrice()" required>
                                </span>
                            </div>
                            <div class="price-section mt-4">
                                <p>Room Total: $<span id="roomTotal">0</span></p>
                                <p>Bed Total: $<span id="bedTotal">0</span></p>
                                <p>Meal Total: $<span id="mealTotal">0</span></p>
                                <h4>Final Total: $<span id="finalTotal">0</span></h4>
                            </div>
                        </div>
                    </div>
                    <div class="footer mt-4 text-end">
                        <button type="submit" class="btn btn-success" name="guestdetailsubmit">Book Now</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Rooms Section -->
    <section id="secondsection" class="py-5">
        <div class="container">
            <h1 class="head text-center">≼ Our Rooms ≽</h1>
            <div class="row roomselect g-4">
                <div class="col-md-3 col-sm-6">
                    <div class="roombox">
                        <div class="hotelphoto h1"></div>
                        <div class="roomdata">
                            <h2>Superior Room</h2>
                            <div class="services mb-3">
                                <i class="fa-solid fa-wifi me-2"></i>
                                <i class="fa-solid fa-burger me-2"></i>
                                <i class="fa-solid fa-spa me-2"></i>
                                <i class="fa-solid fa-dumbbell me-2"></i>
                                <i class="fa-solid fa-person-swimming"></i>
                            </div>
                            <button class="btn btn-primary bookbtn" onclick="openbookbox()">Book</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="roombox">
                        <div class="hotelphoto h2"></div>
                        <div class="roomdata">
                            <h2>Deluxe Room</h2>
                            <div class="services mb-3">
                                <i class="fa-solid fa-wifi me-2"></i>
                                <i class="fa-solid fa-burger me-2"></i>
                                <i class="fa-solid fa-spa me-2"></i>
                                <i class="fa-solid fa-dumbbell"></i>
                            </div>
                            <button class="btn btn-primary bookbtn" onclick="openbookbox()">Book</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="roombox">
                        <div class="hotelphoto h3"></div>
                        <div class="roomdata">
                            <h2>Guest Room</h2>
                            <div class="services mb-3">
                                <i class="fa-solid fa-wifi me-2"></i>
                                <i class="fa-solid fa-burger me-2"></i>
                                <i class="fa-solid fa-spa"></i>
                            </div>
                            <button class="btn btn-primary bookbtn" onclick="openbookbox()">Book</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="roombox">
                        <div class="hotelphoto h4"></div>
                        <div class="roomdata">
                            <h2>Single Room</h2>
                            <div class="services mb-3">
                                <i class="fa-solid fa-wifi me-2"></i>
                                <i class="fa-solid fa-burger"></i>
                            </div>
                            <button class="btn btn-primary bookbtn" onclick="openbookbox()">Book</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Facilities Section -->
    <section id="thirdsection" class="py-5">
        <div class="container">
            <h1 class="head text-center">≼ Facilities ≽</h1>
            <div class="facility row g-4 justify-content-center">
                <div class="col-md-3 col-sm-3">
                    <div class="box">
                        <img src="./image/SwimingPool.png" alt="Swimming Pool" class="facility-img">
                        <h2>Swimming Pool</h2>
                    </div>
                </div>
                <div class="col-md-3 col-sm-3">
                    <div class="box">
                        <img src="./image/spa.jpg" alt="Spa" class="facility-img">
                        <h2>Spa</h2>
                    </div>
                </div>
                <div class="col-md-3 col-sm-3">
                    <div class="box">
                        <img src="./image/food.png" alt="Restaurant" class="facility-img">
                        <h2>24/7 Restaurants</h2>
                    </div>
                </div>
                <div class="col-md-3 col-sm-3">
                    <div class="box">
                        <img src="./image/Gym.jpeg" alt="Gym" class="facility-img">
                        <h2>24/7 Gym</h2>
                    </div>
                </div>
                <div class="col-md-3 col-sm-3">
                    <div class="box">
                        <img src="./image/Heli.png" alt="Heli Service" class="facility-img">
                        <h2>Heli Service</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Updated Footer with Balanced Sections -->
    <footer id="contactus">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <h4>About Us</h4>
                    <p>Welcome to Holiday Inn, where luxury meets unparalleled hospitality. Nestled in the heart of Colombo, our hotel is a sanctuary of comfort and elegance, designed to cater to both leisure and business travelers. With a rich legacy of excellence, we pride ourselves on offering world-class accommodations, state-of-the-art facilities.</p>
                </div>
                <div class="col-md-4">
                    <h4>Contact Information</h4>
                    <p><i class="fas fa-envelope me-2"></i>info@holidayinn.com</p>
                    <p><i class="fas fa-phone me-2"></i>0117 727 729</p>
                    <div class="social mt-3">
                        <a href="#"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#"><i class="fa-brands fa-facebook"></i></a>
                        <a href="#"><i class="fa-brands fa-twitter"></i></a>
                    </div>
                </div>
                <div class="col-md-4">
                    <h4>Navigation</h4>
                    <ul class="list-unstyled">
                        <li><a href="#firstsection">Home</a></li>
                        <li><a href="#secondsection">Rooms</a></li>
                        <li><a href="#thirdsection">Facilities</a></li>
                        <li><a href="#contactus">Contact</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p class="mb-0">© <?php echo date('Y'); ?> Holiday Inn. All Rights Reserved. | Developed by <a href="#">CINEC SE Students</a></p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script>
        var bookbox = document.getElementById("guestdetailpanel");

        function openbookbox() {
            bookbox.style.display = "flex";
        }
        function closebox() {
            bookbox.style.display = "none";
        }

        // Theme toggle functionality
        const themeToggle = document.getElementById('themeToggle');
        const body = document.body;

        themeToggle.addEventListener('click', () => {
            if (body.getAttribute('data-theme') === 'dark') {
                body.removeAttribute('data-theme');
                themeToggle.innerHTML = '<i class="fas fa-moon"></i>';
            } else {
                body.setAttribute('data-theme', 'dark');
                themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
            }
            localStorage.setItem('theme', body.getAttribute('data-theme') || 'light');
        });

        // Load saved theme
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            body.setAttribute('data-theme', 'dark');
            themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
        }

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
</body>
</html>