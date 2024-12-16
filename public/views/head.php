<!DOCTYPE html>
<html :class="{ 'theme-dark': dark }" x-data="data()" lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>easy park</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="./assets/css/tailwind.output.css" />
    <script
      src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js"
      defer
    ></script>
    <script src="./assets/js/init-alpine.js"></script>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.css"
    />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script
      src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"
      defer
    ></script>
    <script src="./assets/js/charts-lines.js" defer></script>
    <script src="./assets/js/charts-pie.js" defer></script>
    <style>
      
        #map-container {
          margin-left: 3%;
            margin-top: 5%;
            width: 95%;
            max-width: 1200px;
            height: 600px;
            display: flex;
            border: 3px solid #1a73e8;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        #map {
            flex-grow: 1;
            position: relative;
            z-index: 1;
        }
        #sidebar {
            width: 300px;
            background-color: white;
            box-shadow: -2px 0 5px rgba(0,0,0,0.1);
            overflow-y: auto;
            padding: 10px;
        }
        .place-card {
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            margin-bottom: 10px;
            padding: 10px;
            transition: box-shadow 0.3s ease;
        }
        .place-card:hover {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .place-card h3 {
            margin: 0 0 8px 0;
            font-size: 0.9em;
            color: #1a73e8;
        }
        .place-card .details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 4px;
            color: #5f6368;
            font-size: 0.8em;
        }
        .material-icons {
            vertical-align: middle;
            margin-right: 5px;
            font-size: 1em;
            color: #1a73e8;
        }
        .search-container {
            position: absolute;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            width: 250px;
        }
        #search-input {
            width: 100%;
            padding: 8px;
            border-radius: 15px;
            border: 1px solid #ddd;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            font-size: 0.9em;
        }

        @media screen and (max-width: 1024px) {
    #map-container {
        flex-direction: column;
        height: auto;
        margin: 2% 1%;
        width: 98%;
    }

    #sidebar {
        width: 100%;
        max-height: 300px;
        order: 2;
    }

    #map {
        height: 400px;
        order: 1;
    }

    .search-container {
        position: relative;
        top: 0;
        left: 0;
        transform: none;
        margin: 10px;
        width: calc(100% - 20px);
    }

    .place-card .details {
        grid-template-columns: 1fr;
    }
}

@media screen and (max-width: 600px) {
    #map-container {
        border: none;
        border-radius: 0;
    }

    #map {
        height: 300px;
    }

    .place-card {
        padding: 8px;
    }

    .place-card h3 {
        font-size: 0.8em;
    }

    .place-card .details {
        font-size: 0.7em;
    }

    #search-input {
        font-size: 0.8em;
        padding: 6px;
    }
}

@media screen and (max-width: 375px) {
    #map {
        height: 250px;
    }

    .material-icons {
        font-size: 0.9em;
    }
}

    </style>
  </head>