<?php
    require_once("inc/core.php");

    login_check();
    
    $new_player_count      = $db->query("SELECT NULL FROM players WHERE player_date > SUBDATE(CURDATE(), 1)")->rowCount();
    $new_character_count   = $db->query("SELECT NULL FROM characters WHERE character_date > SUBDATE(CURDATE(), 1)")->rowCount();
    $total_player_count    = $db->query("SELECT NULL FROM players")->rowCount();
    $total_character_count = $db->query("SELECT NULL FROM characters")->rowCount();
?>

<!doctype html>
<html lang="en">

<head>
    <title>Dashboard - Arelith Portal Tracker</title>

    <?php include "inc/header_tags.php"; ?>
</head>

<body>
    <?php include "inc/header.php"; ?>

    <div class="container-fluid">
        <div class="row">
            <?php include "inc/sidebar.php"; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                </div>

                <div class="row">
                    <div class="col-md-4 my-4">
                        <div class="h-100 p-4 text-white bg-dark rounded-3">
                            <h3><span data-feather="user" style="width:40px;height:40px;margin-right:10px;"></span>New Players</h3>
                            <h2 style="color:#00ce68;"><?php echo $new_player_count; ?></h2>
                            <span style="color:#a9a9a9;">Past 24 hours</span>
                        </div>
                    </div>

                    <div class="col-md-4 my-4">
                        <div class="h-100 p-4 text-white bg-dark rounded-3">
                            <h3><span data-feather="gitlab" style="width:40px;height:40px;margin-right:10px;"></span>New Characters</h3>
                            <h2 style="color:#00ce68;"><?php echo $new_character_count; ?></h2>
                            <span style="color:#a9a9a9;">Past 24 hours</span>
                        </div>
                    </div>

                    <div class="col-md-4 my-4">
                        <div class="h-100 p-4 text-white bg-dark rounded-3">
                            <h3><span data-feather="users" style="width:40px;height:40px;margin-right:10px;"></span>Players / Characters</h3>
                            <h2 style="color:#00ce68;"><?php echo $total_player_count." <span style='color:#fff;'>/</span> ".$total_character_count; ?></h2>
                            <span style="color:#a9a9a9;">Total numbers</span>
                        </div>
                    </div>
                </div>

                <h2>New Player & Character Graph</h2>
                <canvas class="my-4 w-100" id="new-player-character-chart" width="900" height="380"></canvas>

                <h2>Information</h2>
                <ul>
                    <li>
                        <span style="font-size:18px;">Statistics are updated every <b><?php echo PORTAL_UPDATE_INTERVAL; ?></b> minutes.</span>
                    </li>
                    <li>
                        <span style="font-size:18px;">All datas are collected from <a target="_blank" style="text-decoration:none;" href="<?php echo PORTAL_URL; ?>">Arelith Portal</a>.</span>
                    </li>
                    <li>
                        <span style="font-size:18px;">Source code can be found under this <a target="_blank" style="text-decoration:none;" href="https://github.com/egebilecen/arelith-portal-tracker">repo</a>.</span>
                    </li>
                </ul>

                <div class="my-4"></div>
            </main>
        </div>
    </div>

    <?php include "inc/js_includes.php"; ?>

    <script>
        // Graphs
        let ctx = document.getElementById('new-player-character-chart');

        <?php
            $year = date("Y", time());
            $month_list = [
                "01-01",
                "02-01",
                "03-01",
                "04-01",
                "05-01",
                "06-01",
                "07-01",
                "08-01",
                "09-01",
                "10-01",
                "11-01",
                "12-01"
            ];

            $new_player_data    = [];
            $new_character_data = [];

            for($i=0; $i < count($month_list); $i++)
            {
                $date = $year."-".$month_list[$i];
                
                if(new DateTime($date) > new DateTime())
                    continue;

                $date2;

                if($i == count($month_list) - 1)
                    $date2 = ($year + 1)."-01-01";
                else
                    $date2 = $year."-".$month_list[$i + 1];

                array_push($new_player_data,    $db->query("SELECT NULL FROM players WHERE player_date BETWEEN '".$date."' AND '".$date2."'")->rowCount());
                array_push($new_character_data, $db->query("SELECT NULL FROM characters WHERE character_date BETWEEN '".$date."' AND '".$date2."'")->rowCount());
            }
        ?>

        let new_player_data    = [ <?php echo implode(",", $new_player_data); ?> ];
        let new_character_data = [ <?php echo implode(",", $new_character_data); ?> ];

        let new_player_chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [
                    'January',
                    'February',
                    'March',
                    'April',
                    'May',
                    'June',
                    'July',
                    'August',
                    'September',
                    'October',
                    'November',
                    'December'
                ],
                datasets: [
                    {
                        label : "New Players",
                        data: new_player_data,
                        lineTension: 0,
                        backgroundColor: 'transparent',
                        borderColor: 'orange',
                        borderWidth: 4,
                        pointBackgroundColor: 'orange'
                    },
                    {
                        label : "New Characters",
                        data: new_character_data,
                        lineTension: 0,
                        backgroundColor: 'transparent',
                        borderColor: '#8862e0',
                        borderWidth: 4,
                        pointBackgroundColor: '#8862e0'
                    }
                ]
            },
            options: {
                responsive : true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                stacked : false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Data of Year <?php echo date("Y", time()); ?>'
                    },
                    legend: {
                        display: true
                    }
                },
                scales: {
                    y: {
                        ticks: {
                            beginAtZero: true,
                            // stepSize : 5,
                            callback: function(value) {if (value % 1 === 0) {return value;}}
                        }
                    }
                }
            }
        });
    </script>

    <?php include "inc/footer_js.php"; ?>
</body>

</html>
