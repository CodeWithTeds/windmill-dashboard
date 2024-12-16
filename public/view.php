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

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
<div id="map-container">
    <div id="map"></div>
    <div id="sidebar">
        <h2 style="font-size: 1em; margin-bottom: 10px;">Parking Locations</h2>
        <div id="places-list"></div>
    </div>
    <div class="search-container">
        <input type="text" id="search-input" placeholder="Search parking locations...">
    </div>
</div>

<?= require('include/view-info.php') ?>