<?php
session_start();

// Authentication check
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require('views/head.php');
require('views/sidebar.php');
require('views/header.php');
?>
<main class="h-full overflow-y-auto bg-white">
    <div class="container max-w-7xl px-6 mx-auto py-20">
        <!-- Hero Section with Image and Text -->
        <form class="flex flex-col md:flex-row items-center gap-16 mb-16">
            <div class="md:w-1/2 text-left space-y-6">
                <h1 class="text-6xl font-bold text-blue-800 leading-tight tracking-tight mb-6">
                    Welcome to Easy Car Park

                </h1>

                <p class="text-xl text-blue-600 leading-relaxed mb-8 max-w-2xl text-justify">
                    Say goodbye to the stress of searching for parking! Easy Car Park helps you locate available spaces in Muntinlupa City with real-time updates and seamless navigation.
                </p>

                <div class="space-y-4 text-blue-600">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Locate available parking spaces near your location</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Access real-time information about availability</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Navigate easily to your chosen parking area</span>
                    </div>
                </div>
            </div>

            <div class="md:w-1/2">
                <!-- Animated image container -->
                <div class="relative animate-float">
                    <!-- Main image with hover effect -->
                    <img src="assets/img/image.png"
                        alt="Parking Illustration"
                        class="w-full h-auto object-cover rounded-2xl shadow-2xl transform hover:scale-105 transition duration-500 relative z-10">
                </div>
            </div>
        </form>
    
       
<?php require('./views/footer.php'); ?>

<!-- Add this CSS in your head section or stylesheet -->
<style>
    body {
        font-family: 'Space Grotesk', sans-serif;
    }

    h1 {
        background: linear-gradient(to right, #1e40af, #3b82f6);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    @keyframes float {
        0% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-20px);
        }

        100% {
            transform: translateY(0px);
        }
    }

    .animate-float {
        animation: float 6s ease-in-out infinite;
    }

    @import url("https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap");

    .wrapper .title {
        text-align: center;
    }

    .card_Container {
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
        margin: 40px 0;
    }

    .card_Container .card {
        position: relative;
        width: 300px;
        height: 400px;
        margin: 20px;
        overflow: hidden;
        box-shadow: 0 30px 30px -20px rgba(0, 0, 0, 1),
            inset 0 0 0 1000px rgba(67, 52, 109, .6);
        border-radius: 15px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .card .imbBx,
    .imbBx img {
        width: 100%;
        height: 100%;
    }

    .card .content {
        position: absolute;
        bottom: -160px;
        width: 100%;
        height: 160px;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        backdrop-filter: blur(15px);
        box-shadow: 0 -10px 10px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        transition: bottom 0.5s;
        transition-delay: 0.65s;
    }

    .card:hover .content {
        bottom: 0;
        transition-delay: 0s;
    }

    .content .contentBx h3 {
        text-transform: uppercase;
        color: #fff;
        letter-spacing: 2px;
        font-weight: 600;
        font-size: 20px;
        text-align: center;
        margin: 20px 0 15px;
        line-height: 1.1em;
        transition: 0.5s;
        transition-delay: 0.6s;
        opacity: 0;
        transform: translateY(-20px);
    }

    .card:hover .content .contentBx h3 {
        opacity: 1;
        transform: translateY(0);
    }

    .content .contentBx h3 span {
        font-size: 14px;
        font-weight: 400;
        text-transform: initial;
        color: #3b82f6;
    }

    .content .sci {
        position: relative;
        bottom: 10px;
        display: flex;
    }

    .content .sci li {
        list-style: none;
        margin: 0 10px;
        transform: translateY(40px);
        transition: 0.5s;
        opacity: 0;
        transition-delay: calc(0.2s * var(--i));
    }

    .card:hover .content .sci li {
        transform: translateY(0);
        opacity: 1;
    }

    .content .sci li a {
        color: #fff;
        font-size: 24px;
    }
</style>