<?php
    require_once("inc/core.php");

    login_check();

    if(!g("name", false)
    || !$character_data = get_character_data_from_name(g("name", false)))
    {
        header("Location: main");
    }

    $character_activity = get_character_activity_from_name($character_data["character_name"]);
    $guessed_play_time = 0;

    $day_activity_list = [];
    $day_activity_count = [
        "Monday"    => 0,
        "Tuesday"   => 0,
        "Wednesday" => 0,
        "Thursday"  => 0,
        "Friday"    => 0,
        "Saturday"  => 0,
        "Sunday"    => 0
    ];

    foreach($character_activity as $_ => $row)
    {
        $activity = $row["character_activity_date"];
        $date     = convert_time_str($activity);
        $day_name = $date->format("l");
        
        $day_activity_count[$day_name] += 1;
        $guessed_play_time += PORTAL_UPDATE_INTERVAL;

        if(!key_exists($day_name, $day_activity_list))
            $day_activity_list[$day_name] = [];

        array_push($day_activity_list[$day_name], $activity);
    }
?>

<!doctype html>
<html lang="en">

<head>
    <title><?php echo g("name"); ?> - Arelith Portal Tracker</title>

    <?php include "inc/header_tags.php"; ?>
</head>

<body>
    <?php include "inc/header.php"; ?>

    <div class="container-fluid">
        <div class="row">
            <?php include "inc/sidebar.php"; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 border-bottom">
                    <h1 class="h2">
                        <?php echo g("name"); ?>
                        <span style="display:block;font-size:16px;">Player(s): 
                            <?php
                                $player_name_list = [];
                                $player_id_list   = explode(STR_ARRAY_SEPARATOR, $character_data["character_player_id"]);
                                
                                for($i=0; $i < count($player_id_list); $i++)
                                {
                                    $id = $player_id_list[$i];
                                    $player_name = get_player_name_from_id($id);

                                    array_push($player_name_list, "<a style='text-decoration:none;' href='view_player?name=".urlencode($player_name)."'>".$player_name."</a>");
                                }

                                echo implode(", ", $player_name_list);
                            ?>
                        </span>
                        <span style="display:block;font-size:16px;">Last Activity: <span style="color:#a9a9a9;"><?php echo count($character_activity) > 0 ? format_date_from_mysql_date($character_activity[0]["character_activity_date"]) : "-"; ?></span></span>
                        <a href="javascript:void(0);" style="display:block;font-size:16px;text-decoration:none;" onclick="toggle_character_quick_access('<?php echo urlencode(g("name", false)); ?>')">Toggle Quick Access</a>
                    </h1>
                    
                    <?php
                        if($character_data["character_portrait"] != "")
                        {
                    ?>
                            <div style="overflow:hidden;height:172px;display:block;margin-bottom:10px;">
                                <img style="display:block;height:220px;" src="<?php if(!filter_var($character_data["character_portrait"], FILTER_VALIDATE_URL)) echo "https://astrolabe.nwnarelith.com/".$character_data["character_portrait"]; else echo $character_data["character_portrait"]; ?>">
                            </div>
                    <?php
                        }
                    ?>
                </div>

                <div class="row">
                    <div class="col-md-4 my-4">
                        <div class="h-100 p-5 text-white bg-dark rounded-3">
                            <h3><span data-feather="watch" style="width:40px; height:40px; margin-right:10px;"></span>Total Play Time</h3>
                            <h2 style="color:#00ce68;"><span style="color:#fff;">~</span><?php echo number_format($guessed_play_time / 60, 1, ".", ""); ?><span style="color:#fff;">h</span></h2>
                            <span style="color:#a9a9a9;">Guessed</span>
                        </div>
                    </div>

                    <div class="col-md-4 my-4">
                        <div class="h-100 p-5 text-white bg-dark rounded-3">
                            <h3><span data-feather="calendar" style="width:40px; height:40px; margin-right:10px;"></span>Most Active Day</h3>
                            <h2 style="color:#00ce68;"><?php echo array_keys($day_activity_count, max($day_activity_count))[0]; ?></h2>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <h2>Activity</h2>
                        <canvas class="my-4 w-100" id="activity-graph"></canvas>
                    </div>
                    <div class="col-md-6">
                        <h2>Play Time</h2>
                        <canvas class="my-4 w-100" id="play-hours-graph"></canvas>
                    </div>
                </div>
            
                <div class="row">
                    <h2>Activity Log</h2>
                    <div class="col-md-12">
                        <table class="table table-striped table-dark" id="activity-table">
                            <thead>
                                <th>ID</th>
                                <th>Date</th>
                            </thead>
                            <tbody>
                        <?php
                            foreach($character_activity as $i => $row)
                            {
                        ?>
                                <tr>
                                    <td><?php echo count($character_activity) - $i; ?></td>
                                    <td><?php echo format_date_from_mysql_date($character_activity[$i]["character_activity_date"]); ?></td>
                                </tr>
                        <?php
                            }
                        ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="my-4"></div>
            </main>
        </div>
    </div>

    <?php include "inc/js_includes.php"; ?>

    <script>
        $('#activity-table').DataTable({ 
            order : [],
            columns: [
                {
                    width: "5%"
                },
                null
            ]
         });

        // Play Time Graph
        let ctx = document.getElementById('play-hours-graph');

        let chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [
                    "Monday",
                    "Tuesday",
                    "Wednesday",
                    "Thursday",
                    "Friday",
                    "Saturday",
                    "Sunday"
                ],
                datasets: [
                    {
                        label : "Playtime",
                        data: [
                        <?php
                            foreach($day_activity_count as $key => $val)
                            {
                        ?>
                                <?php echo number_format($val * PORTAL_UPDATE_INTERVAL / 60, 1, ".", ""); echo ","; ?>
                        <?php
                            }    
                        ?>
                        ],
                        lineTension: 0,
                        backgroundColor: 'transparent',
                        borderColor: 'orange',
                        borderWidth: 4,
                        pointBackgroundColor: 'orange'
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
                    legend: {
                        display: false
                    },
                    tooltip : {
                        callbacks : {
                            label : (item) => item.label+": "+item.formattedValue+" hour(s)"
                        }
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

        // Activity Graph
        let ctx2 = document.getElementById('activity-graph');

        let data2 = [ 
        <?php
            foreach($day_activity_count as $key => $val)
            {
        ?>
                <?php echo number_format($val * PORTAL_UPDATE_INTERVAL / 60, 1, ".", ""); echo ","; ?>
        <?php
            }    
        ?>
        ];

        let chart2 = new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: [
                    "Monday",
                    "Tuesday",
                    "Wednesday",
                    "Thursday",
                    "Friday",
                    "Saturday",
                    "Sunday"
                ],
                datasets: [
                    {
                        label : "Activity",
                        data: data2,
                        backgroundColor: ["#0074D9", "#FF4136", "#2ECC40", "#FF851B", "#7FDBFF", "#B10DC9", "#FFDC00", "#001f3f", "#39CCCC", "#01FF70", "#85144b", "#F012BE", "#3D9970", "#111111", "#AAAAAA"]
                    }
                ]
            },
            options: {
                responsive : true,
                plugins : {
                    legend : {
                        display: true,
                        position : "bottom"
                    },
                    tooltip : {
                        callbacks : {
                            label : (item) => item.label+": "+item.formattedValue+" hour(s)"
                        }
                    }
                }
            }
        });
    </script>

    <?php include "inc/footer_js.php"; ?>
</body>

</html>
