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
        <div class="wrapper">
            <div class="title">
                <h4 class="text-6xl font-bold mb-6" style="background: linear-gradient(to right, #1e40af, #3b82f6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                    Our Team
                </h4>
            </div>

            <div class="text-center max-w-3xl mx-auto mb-16">
                <p class="text-xl text-blue-600 leading-relaxed mb-8">
                    Meet the talented individuals behind Easy Car Park. Our dedicated team combines expertise in design, development, and user experience to bring you the best parking solution in Muntinlupa City.
                </p>
                <div class="flex justify-center items-center gap-6 text-gray-600">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Expert Developers</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>UI/UX Specialists</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>24/7 Support</span>
                    </div>
                </div>
            </div>

            <div class="card_Container">
                <div class="card">
                    <div class="imbBx">
                        <img src="./assets/img/pic2.jpeg" alt="Team Member 1">
                    </div>
                    <div class="content">
                        <div class="contentBx">
                            <h3>John Doe <br><span>UI/UX Designer</span></h3>
                        </div>
                        <ul class="sci">
                            <li style="--i: 1">
                                <a href="#"><i class="fa-brands fa-instagram"></i></a>
                            </li>
                            <li style="--i: 2">
                                <a href="#"><i class="fa-brands fa-github"></i></a>
                            </li>
                            <li style="--i: 3">
                                <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="imbBx">
                        <img src="./assets/img/pic1.jpeg" alt="Team Member 2">
                    </div>
                    <div class="content">
                        <div class="contentBx">
                            <h3>Mykel Smith <br><span>Front-End Web Developer</span></h3>
                        </div>
                        <ul class="sci">
                            <li style="--i: 1">
                                <a href="#"><i class="fa-brands fa-instagram"></i></a>
                            </li>
                            <li style="--i: 2">
                                <a href="#"><i class="fa-brands fa-github"></i></a>
                            </li>
                            <li style="--i: 3">
                                <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="imbBx">
                        <img src="./assets/img/pic3.jpg" alt="Team Member 3">
                    </div>
                    <div class="content">
                        <div class="contentBx">
                            <h3>Alex Morgan <br><span>Back-End Web Developer</span></h3>
                        </div>
                        <ul class="sci">
                            <li style="--i: 1">
                                <a href="#"><i class="fa-brands fa-instagram"></i></a>
                            </li>
                            <li style="--i: 2">
                                <a href="#"><i class="fa-brands fa-github"></i></a>
                            </li>
                            <li style="--i: 3">
                                <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="mt-20 text-center max-w-4xl mx-auto">
                <h5 class="text-2xl font-bold text-blue-800 mb-4">Why Choose Our Team?</h5>
                <p class="text-lg text-gray-600 mb-8">
                    Our team brings together years of experience in software development, design, and local parking solutions. We're committed to making parking easier and more efficient for everyone in Muntinlupa City.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="p-6 bg-gray-50 rounded-lg">
                        <h6 class="font-bold text-blue-700 mb-2">Innovation</h6>
                        <p class="text-gray-600">Constantly improving our solutions with the latest technology</p>
                    </div>
                    <div class="p-6 bg-gray-50 rounded-lg">
                        <h6 class="font-bold text-blue-700 mb-2">Experience</h6>
                        <p class="text-gray-600">Years of combined experience in parking solutions</p>
                    </div>
                    <div class="p-6 bg-gray-50 rounded-lg">
                        <h6 class="font-bold text-blue-700 mb-2">Dedication</h6>
                        <p class="text-gray-600">Committed to providing the best user experience</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require('./views/footer.php'); ?>

<style>
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