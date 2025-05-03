<?php
require_once 'db_connection.php';

// Initialize session
session_start();

// Function to send Telegram notifications
function sendTelegramNotification($message) {
    $botToken = "7249901987:AAH0MRoTva05NBULGZKaNB6agIkHYuRkgpY";
    $chatId = "1492036636";
    $url = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=" . urlencode($message);
    
    $response = file_get_contents($url);
    if ($response === FALSE) {
        error_log('Telegram API çağrısında hata oluştu.');
    } else {
        error_log("Telegram API Response: " . $response);
    }
}

// Form data handling and database insertion
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input data
    $name = htmlspecialchars($_POST['name']);
    $surname = htmlspecialchars($_POST['surname']);
    $country_code = htmlspecialchars($_POST['country_code']);
    $phone = htmlspecialchars($_POST['phone']);
    $from_location = htmlspecialchars($_POST['from_location']);
    $to_location = htmlspecialchars($_POST['to_location']);
    $reservation_date = htmlspecialchars($_POST['reservation_date']);
    $time = htmlspecialchars($_POST['time']);
    $vehicle = htmlspecialchars($_POST['vehicle']);
    $flight_code = htmlspecialchars($_POST['flight_code']);
    $message = htmlspecialchars($_POST['message']);

    // Prepare and bind SQL statement
    $stmt = $conn->prepare("INSERT INTO reservation (name, surname, country_code, phone, from_location, to_location, reservation_date, time, vehicle, flight_code, message) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssss", $name, $surname, $country_code, $phone, $from_location, $to_location, $reservation_date, $time, $vehicle, $flight_code, $message);

    // Execute SQL statement
    if ($stmt->execute()) {
        $_SESSION['reservation_success'] = true;
        
        // Send Telegram notification
        $notificationMessage = "Yeni rezervasyon eklendi:\nAd: $name\nSoyad: $surname\nNereden: $from_location\nNereye: $to_location\nUlke_kodu: $country_code\nNumara: $phone\nTarih: $reservation_date\nSaat: $time\nAraç: $vehicle\nUcus_kodu: $flight_code\nNot: $message";
        sendTelegramNotification($notificationMessage);
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement
    $stmt->close();

    // Redirect to avoid resubmission on page refresh
    header("Location: index.php");
    exit();
}

// Close database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Kaşlı Travel Agency provides reliable and affordable transportation services in Kaş and Kalkan. Book now for safe airport transfers from Dalaman and Antalya.">
<meta name="keywords" content="Kaşlı Travel Agency, Kaş transportation, Kalkan transportation, airport transfer, Dalaman airport, Antalya airport, travel services">
<meta name="author" content="Kaşlı Travel Agency">

    <title>Kaşlı Travel Agency</title>
    <link rel="icon" type="image/jpg" href="images/icon.jpg">
    <style>
        .top-bar {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 180px;
            background-color: #002560;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            box-sizing: border-box;
        }

        .top-bar img {
            height: 100%;
            object-fit: cover;
        }

        .top-bar .contact-number {
            font-size: 24px;
            color: white;
        }

        .top-bar .social-icons {
            display: flex;
            gap: 10px;
        }

        .top-bar .social-icons img {
            width: 32px;
            height: 32px;
            cursor: pointer;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #fffff0;
            margin: 0;
            padding-top: 120px;
        }

        .contact-section {
            max-height:250px;
            display: flex;
            justify-content: space-between;
            padding: 20px;
            background-color: #002560;
            color: white;
            align-items: flex-start;
        }

        .contact-section h2 {
            margin-bottom: 10px;
        }

        .contact-section a {
            text-decoration: none;
            color: white;
        }

        .contact-section a img {
            width: 32px;
            height: 32px;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .contact-section a img:hover {
            transform: scale(1.2);
        }

        .contact-section .contact-left {
            margin-left:20%;
            width: 40%;
        }

        .contact-section .contact-right {
            width: 60%;
            position: relative;
        }

        #map {
            width: 70%;
            height: 240px;
        }

        .content {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            gap: 20px;
            padding: 20px;
        }

	.reservation-container {
	    background-color: #f0f8ff;
	    padding: 20px;
	    border-radius: 20px;
	    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
	    width: 424px;
	    margin-top: 10px;
	}
	.vehicle-boxes {
	    margin-top: 10px;
	    width: 500px;
	    padding: 10px;
	    border-radius: 20px;
	}
	
	.vehicle-boxes .box {
	    background-color: #002560;
	    height: 214px;
	    width: 100%;
	    margin-bottom: 5px;
	    border-radius: 20px;
	    display: flex;
	    flex-direction: column;
	    align-items: center;
	    padding: 10px;
	    position: relative;
	}
	
	.vehicle-boxes .photos {
	    display: flex;
	    justify-content: space-around;
	    width: 100%;
	    margin-top: 30px;
	}
	
	.vehicle-boxes .photos img {
	    width: 25%;
	    height: 85%;
	    margin: 10px;
	    cursor: pointer;
	    transition: transform 0.2s;
	}
	
	.vehicle-boxes .photos img:hover {
	    transform: scale(1.05);
	}
	
	.vehicle-boxes .underline {
	    width: 100%;
	    height: 2px;
	    background-color: white;
	    margin: 5px 0;
	    margin-bottom: 10px;
	}
	
	.vehicle-boxes .captions {
	    display: flex;
	    justify-content: space-around;
	    width: 100%;
	}
	
	.vehicle-boxes .caption-box {
	    background-color: white;
	    padding: 10px;
	    border-radius: 10px;
	    text-align: center;
	    width: 50px;
	}
	
	.details-title {
	    position: absolute;
	    top: 10px;
	    left: 50%;
	    transform: translateX(-50%);
	    width: 90%;
	    background-color: rgba(0, 0, 0, 0.5);
	    color: white;
	    text-align: center;
	    padding: 5px;
	    font-size: 18px;
	    z-index: 1;
	    border-radius: 20px;
	}
	
	.lightbox {
	    display: none;
	    position: fixed;
	    top: 0;
	    left: 0;
	    width: 100%;
	    height: 100%;
	    background-color: rgba(0, 0, 0, 0.8);
	    justify-content: center;
	    align-items: center;
	    z-index: 1000;
	}
	
	.lightbox-img {
	    max-width: 80%;
	    max-height: 80%;
	}
	
	.close {
	    position: absolute;
	    top: 20px;
	    right: 30px;
	    color: white;
	    font-size: 30px;
	    font-weight: bold;
	    cursor: pointer;
	}
	
	
	
	#form-container {
        background-color: #f0f8ff;
	color:black;
   	 }


        .input-group {
            display: flex;
            flex-direction: row;
            margin-bottom: 15px;
        }

        .input-group input,
        .input-group select,
        .input-full textarea {
            width: 100%;
            margin-bottom: 10px;
            padding: 22px;
            border: 1px solid #ccc;
            border-radius: 20px;
            box-sizing: border-box;
            font-size: 16px;
        }

        .input-full textarea {
            width: 100%;
            height: 250px;
        }    

        .input-group .location-select {
            width: 322%;
        }

        .input-group select.country-code {
            background: url('data:image/svg+xml;utf8,<svg fill="%23333" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>') no-repeat right 10px center;
            background-color: white;
            background-size: 12px 12px;
        }

        .button-container {
            text-align: center;
        }

        .button-container button {
            padding: 10px 20px;
            border: none;
            border-radius: 12px;
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
            width: 100%;
            max-width: 200px;
        }

        select:focus,
        input:focus,
        textarea:focus {
            border-color: #4CAF50;
            outline: none;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
        }

        .text-container {
            display: flex;
            justify-content: center;
            width: 100%;
            margin-top: 10px;
        }

        .text {
            text-align: center;
            background-color: #385D8D;
            width: 60%;
            padding: 20px;
            border-radius: 20px;
            margin-top: 20px;
            color: white;
        }

        .photos {
            display: flex;
            transition: transform 0.5s ease-in-out;
            margin-bottom: 20px;
        }

        .photo {
            min-width: 24%;
            max-width: 24%;
            height: auto;
            margin-left: 12px;
        }

        .prev, .next {
            display: none;
        }

        @media only screen and (max-width: 600px) {
        
	/*
	body {
	    font-family: Arial, sans-serif;
	    background-color: #fff5ee;
	    margin: 0;
	    padding-top: 120px;
	}
	*/
	

            .contact-section .contact-left {
                margin-left:0%;
                width: 40%;
            }

            .top-bar {
                flex-direction: column;
                justify-content: center;
                padding: 0;
                height: auto;
            }

            .top-bar img {
                height: auto;
                width: 100%;
            }

            .social-icons {
                display: flex;
                justify-content: center;
                margin-top: 10px;
            }

            .social-icons a img {
                width: 32px;
                height: 32px;
                cursor: pointer;
            }

            .contact-number {
                margin-top: 10px;
            }

            .content {
                flex-direction: column;
                align-items: center;
            }

            .reservation-container,
            .vehicle-boxes {
                width: 100%;
            }

            .input-group {
                flex-direction: row;
                flex-wrap: wrap;
                width: 100%;
            }

            .input-group input,
            .input-group select,
            .input-full textarea {
                width: calc(50% - 5px);
                margin-bottom: 10px;
               	padding: 10px;
            }

            .input-group .location-select {
                width: 332%;
            }

            .input-group input[type="date"] {
                width: 50%;
            }

            .input-full textarea {
                width: 100%;
                height: 150px;
            }

            .text-container {
                display: flex;
                justify-content: center;
                width: 100%;
            }

            .text {
                text-align: center;
                background-color: #385D8D;
                width: 100%;
                padding: 20px;
                border-radius: 40px;
                margin-top: 40px;
                color: white;
            }

            .photo-slider {
                position: relative;
                max-width: 94%;
                margin: auto;
                overflow: hidden;
            }

            .photos {
                display: flex;
                transition: transform 0.5s ease-in-out;
                margin-bottom: 20px;
            }

            .photo {
                min-width: 100%;
                max-width: 100%;
                height: auto;
            }

            .prev, .next {
                display: block;
                position: absolute;
                top: 50%;
                width: auto;
                padding: 16px;
                margin-top: -22px;
                color: white;
                font-weight: bold;
                font-size: 18px;
                cursor: pointer;
                background-color: rgba(0, 0, 0, 0.5);
                border: none;
                border-radius: 3px;
                z-index: 1;
            }

            .prev {
                left: 0;
            }

            .next {
                right: 0;
            }
        }
    </style>
</head>
<body>

    <div class="top-bar">
        <img src="images/logooo.jpg" alt="Kasli Travel Agency logo" >
        <div class="contact-number">
            <a href="tel:+905415257002" style="text-decoration: none; color: white;">+90 (541) 525 7002</a></br>
            <a href="tel:+905350217002" style="text-decoration: none; color: white;">+90 (535) 021 7002</a>
        </div>
        <div class="social-icons">
            <a href="https://www.instagram.com/kaslitravelagency?igsh=eWk5bTYyZWE2c3Jm&utm_source=qr" target="_blank">
                <img src="images/instagram.png" alt="Instagram">
            </a>
            <a href="https://t.me/Kasli_travel_agency" target="_blank">
                <img src="images/telegram.png" alt="Telegram">
            </a>
            <a href="https://wa.me/+905350217002" target="_blank">
                <img src="images/whatsapp.png" alt="WhatsApp">
            </a>
        </div>
    </div>

    <div class="text-container" style="margin-top:100px;">
        <div class="text">
            <h1>Welcome to Kaşlı Travel Agency</h1>
            <p>We are here for your safe and comfortable journey. You can easily reach us by filling out the reservation form below.</p>
        </div>
    </div>

    <div class="content">
        <div class="reservation-container">
        <div id="form-container">
            <h1>Make a Reservation</h1>
            <form method="POST" action="index.php" onsubmit="return validateForm()">
                <div class="input-group">
                    <input type="text" name="name" placeholder="Name" required>
                    <input type="text" name="surname" placeholder="Last Name" required>
                </div>
                <div class="input-group">
                    <select name="country_code" class="country-code">
                        <option value="+90">+90 Turkey</option>
                        <option value="+1">+1 USA</option>
                        <option value="+44">+44 UK</option>
                        <option value="+49">+49 Germany</option>
                        <option value="+33">+33 France</option>
                        <option value="+39">+39 Italy</option>
                        <option value="+34">+34 Spain</option>
                        <option value="+30">+30 Greece</option>
                        <option value="+31">+31 Netherlands</option>
                        <option value="+32">+32 Belgium</option>
                        <option value="+350">+350 Gibraltar</option>
                        <option value="+351">+351 Portugal</option>
                        <option value="+352">+352 Luxembourg</option>
                        <option value="+353">+353 Ireland</option>
                        <option value="+354">+354 Iceland</option>
                        <option value="+355">+355 Albania</option>
                        <option value="+356">+356 Malta</option>
                        <option value="+357">+357 Cyprus</option>
                        <option value="+358">+358 Finland</option>
                        <option value="+358 18">+358 18 Åland</option>
                        <option value="+359">+359 Bulgaria</option>
                        <option value="+36">+36 Hungary</option>
                    </select>
                    <input type="text" name="phone" placeholder="Phone" class="phone" required>
                </div>
                <div class="input-group">
                    <div id="from_location_container">
                        <select name="from_location" id="from_location" class="location-select" onchange="toggleOtherInput('from')" required>
                            <option value="" selected disabled>From</option>
                            <option value="Antalya">Antalya</option>
                            <option value="Kaş">Kaş</option>
                            <option value="Kalkan">Kalkan</option>
                            <option value="Dalaman">Dalaman</option>
                            <option value="Diğer">Other</option>
                        </select>
                    </div>
                </div>
                <div class="input-group">
                    <div id="to_location_container">
                        <select name="to_location" id="to_location" class="location-select" onchange="toggleOtherInput('to')" required>
                            <option value="" selected disabled>To</option>
                            <option value="Antalya">Antalya</option>
                            <option value="Kaş">Kaş</option>
                            <option value="Kalkan">Kalkan</option>
                            <option value="Dalaman">Dalaman</option>
                            <option value="Diğer">Other</option>
                        </select>
                    </div>
                </div>

                <div class="input-group">
                    <input type="date" name="reservation_date" id="reservation_date" required>
                    <input type="time" name="time" required>
                </div>
                <div class="input-group">
                    <select name="vehicle" required>
                        <option value="" selected disabled>Vehicle</option>
                        <option value="sedan">Sedan</option>
                        <option value="minivan">Minivan</option>
                        <option value="minibus">Minibus</option>
                    </select>
                    <input type="text" name="flight_code" placeholder="Flight Code">
                </div>

                <div class="input-full">
                    <textarea name="message" placeholder="Write your message"></textarea>
                </div>

                <div class="button-container">
                    <button type="submit" id="applyButton">Send</button>
                </div>
            </form>
        </div>
        </div>

	<div class="vehicle-boxes">
	<div class="box">
	        <div class="details-title">Dalaman Airport-Kalkan</div>
	        <div class="photos">
	            <img src="images/araba.jpg" alt="Sedan vehicle" onclick="openLightbox(this)">
	            <img src="images/minivann.jpg" alt="Minivan vehicle" onclick="openLightbox(this)">
	            <img src="images/minibuss.jpg" alt="Minibus vehicle" onclick="openLightbox(this)">
	        </div>
	        <div class="underline"></div>
	        <div class="captions">
	            <div class="caption-box">€80</div>
	            <div class="caption-box">€90</div>
	            <div class="caption-box">€170</div>
	        </div>
	    </div>
	<div class="box">
	        <div class="details-title">Antalya Airport-Kalkan</div>
	        <div class="photos">
	            <img src="images/araba.jpg" alt="Sedan vehicle" onclick="openLightbox(this)">
	            <img src="images/minivann.jpg" alt="Minivan vehicle" onclick="openLightbox(this)">
	            <img src="images/minibuss.jpg" alt="Minibus vehicle" onclick="openLightbox(this)">
	        </div>
	        <div class="underline"></div>
	        <div class="captions">
	            <div class="caption-box">€100</div>
	            <div class="caption-box">€130</div>
	            <div class="caption-box">€250</div>
	        </div>
	    </div>
	<div class="box">
	        <div class="details-title">Dalaman Airport-Kaş</div>
	        <div class="photos">
	            <img src="images/araba.jpg" alt="Sedan vehicle" onclick="openLightbox(this)">
	            <img src="images/minivann.jpg" alt="Minivan vehicle" onclick="openLightbox(this)">
	            <img src="images/minibuss.jpg" alt="Minibus vehicle" onclick="openLightbox(this)">
	        </div>
	        <div class="underline"></div>
	        <div class="captions">
	            <div class="caption-box">€90</div>
	            <div class="caption-box">€100</div>
	            <div class="caption-box">€180</div>
	        </div>
	    </div>
	<div class="box">
	        <div class="details-title"> Antalya Airport-Kaş</div>
	        <div class="photos">
	            <img src="images/araba.jpg" alt="Sedan vehicle" onclick="openLightbox(this)">
	            <img src="images/minivann.jpg" alt="Minivan vehicle" onclick="openLightbox(this)">
	            <img src="images/minibuss.jpg" alt="Minibus vehicle" onclick="openLightbox(this)">
	        </div>
	        <div class="underline"></div>
	        <div class="captions">
	            <div class="caption-box">€100</div>
	            <div class="caption-box">€120</div>
	            <div class="caption-box">€230</div>
	        </div>
	    </div>
	</div>
	
	<div id="lightbox" class="lightbox" onclick="closeLightbox()">
	    <span class="close">&times;</span>
	    <img class="lightbox-img" src="" alt="Enlarged view">
	</div>

    </div>
    <div class="text-container" style="margin-bottom:20px;">
        <div class="text">
            <h2 style="color:white;">Professional and Affordable Transportation Services</h2>
            <p>As KASLİ TRAVEL AGENCY, our goal is to provide professional and affordable transportation opportunities to our customers throughout Kaş and Kalkan. Thanks to our versatile and reliable services, you can spend time on the things that are important to you. Leave the transportation to us. Sit back, relax, and get where you need to go safely.</p>
        </div>
    </div>

    <div class="photo-slider">
        <button class="prev" onclick="plusSlides(-1)">&#10094;</button>
        <button class="next" onclick="plusSlides(1)">&#10095;</button>
        <div class="photos">
            <img src="images/a.jpg" alt="Scenic view of Kaş" class="photo">
            <img src="images/b.jpg" alt="Beautiful beach in Kalkan" class="photo">
            <img src="images/c.jpg" alt="Dalaman airport" class="photo">
            <img src="images/d.jpg" alt="Antalya cityscape" class="photo">
        </div>
    </div>

    <div class="contact-section">
        <div class="contact-left">
            <h1 style="color:white; margin-bottom:20px;">Contact Us</h1>
            <a href="tel:+905415257002" style="text-decoration: none; color: white; display: block; margin-bottom: 20px; margin-left:0px;">+90 (541) 525 7002</a>
            <a href="tel:+905350217002" style="text-decoration: none; color: white;display: block; margin-bottom: 20px; margin-left:0px;"">+90 (535) 021 7002</a>
            <div class="social-icons">
                <a href="https://www.instagram.com/kaslitravelagency?igsh=eWk5bTYyZWE2c3Jm&utm_source=qr" target="_blank">
                    <img src="images/instagram.png" alt="Instagram" style="margin-right:10px;">
                </a>
                <a href="https://t.me/Kasli_travel_agency" target="_blank">
                    <img src="images/telegram.png" alt="Telegram" style="margin-right:10px;">
                </a>
                <a href="https://wa.me/++905350217002" target="_blank">
                    <img src="images/whatsapp.png" alt="WhatsApp">
                </a>
            </div>
        </div>
        <div class="contact-right">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3219.0607221931805!2d29.676032600000003!3d36.213719!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14c1db9b22c90b73%3A0xe38ad2eafc9896f5!2sKa%C5%9Fl%C4%B1%20Travel%20Agency%20-%20Dalaman%20%26%20Antalya%20Airport%20Transfer!5e0!3m2!1str!2str!4v1719530683331!5m2!1str!2str"  style="border:10px;margin-top:35px;width:98%;height:100%;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
    
<div class="tursab-verification" style="text-align:center; margin: 20px 0; border: 3px solid #ccc; background-color: #f0f8ff; padding: 20px; border-radius: 20px;">
    <img src="images/TURSAB.png" alt="TURSAB Verified" style="max-width: 100px; margin-bottom: 10px;">
    <p style="color:black;">Kaşlı Travel Agency is a TURSAB certified travel agency. You can trust our services for safe and reliable transportation.</p>
    <p style="color:black;">Certificate Number: 8214</p>
    <a href="https://www.tursab.org.tr" target="_blank" style="color:blue; text-decoration: underline;">Verify TURSAB Membership</a>
</div>


    <footer style=" text-align:center;" >
        <p style="color:black;">&copy; 2024 kaslitravelagency.com Tüm hakları saklıdır.</p>
        <p style="color:black;">Created by <span style="color:red;">BARAN ÇELİK</span> </p>
        <p style="color:black;">For Contact: <a href="mailto:celikbaran4865@gmail.com">celikbaran4865@gmail.com</a> </p>
    </footer>

    <script>
    
    
    	function openLightbox(image) {
	    const lightbox = document.getElementById("lightbox");
	    const lightboxImg = lightbox.querySelector(".lightbox-img");
	    lightboxImg.src = image.src;
	    lightbox.style.display = "flex";
	}
	
	function closeLightbox() {
	    const lightbox = document.getElementById("lightbox");
	    lightbox.style.display = "none";
	}
	


        let slideIndex = 0;

        function showSlides(index) {
            const slides = document.querySelectorAll('.photo-slider .photo');
            if (index >= slides.length) {
                slideIndex = 0;
            }
            if (index < 0) {
                slideIndex = slides.length - 1;
            }
            const offset = -slideIndex * 100;
            document.querySelector('.photo-slider .photos').style.transform = `translateX(${offset}%)`;
        }

        function plusSlides(n) {
            slideIndex += n;
            showSlides(slideIndex);
        }

        document.addEventListener('DOMContentLoaded', function() {
            var today = new Date().toISOString().split('T')[0];
            document.getElementById("reservation_date").setAttribute('min', today);

            <?php
            if (isset($_SESSION['reservation_success']) && $_SESSION['reservation_success']) {
                echo 'alert("Reservation successfully created.");';
                unset($_SESSION['reservation_success']);
            }
            ?>
        });

        function toggleOtherInput(locationType) {
            var select;
            var container;

            if (locationType === 'from') {
                select = document.getElementById("from_location");
                container = document.getElementById("from_location_container");
            } else if (locationType === 'to') {
                select = document.getElementById("to_location");
                container = document.getElementById("to_location_container");
            }

            if (select.value === "Diğer") {
                var inputField = document.createElement("input");
                inputField.type = "text";
                inputField.name = locationType === 'from' ? "from_location" : "to_location";
                inputField.placeholder = "Write here";
                inputField.required = true;
                container.innerHTML = '';
                container.appendChild(inputField);

                inputField.style.width = "144%";
                inputField.style.marginBottom = "10px";
                inputField.style.padding = "10px";
                inputField.style.border = "1px solid #ccc";
                inputField.style.borderRadius = "20px";
                inputField.style.boxSizing = "border-box";
                inputField.style.fontSize = "16px";
            } else {
                container.innerHTML = '';
                var newSelect = document.createElement("select");
                newSelect.name = locationType === 'from' ? "from_location" : "to_location";
                newSelect.id = locationType === 'from' ? "from_location" : "to_location";
                newSelect.setAttribute("onchange", "toggleOtherInput('" + locationType + "')");

                var defaultOption = document.createElement("option");
                defaultOption.value = "";
                defaultOption.textContent = locationType === 'from' ? "From" : "To";
                newSelect.appendChild(defaultOption);

                var options = ["Antalya", "Kaş", "Kalkan", "Dalaman", "Diğer"];
                options.forEach(option => {
                    var opt = document.createElement("option");
                    opt.value = option;
                    opt.textContent = option;
                    newSelect.appendChild(opt);
                });

                newSelect.value = select.value;

                container.appendChild(newSelect);

                newSelect.className = "location-select";
                newSelect.style.width = "332%";
            }
        }

        function validateForm() {
            var fromLocation = document.getElementById("from_location").value;
            var toLocation = document.getElementById("to_location").value;

            if (!fromLocation) {
                alert("Please select a 'From' location.");
                return false;
            }
            if (!toLocation) {
                alert("Please select a 'To' location.");
                return false;
            }

            return true;
        }

    </script>
</body>
</html>
