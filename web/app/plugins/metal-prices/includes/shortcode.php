<?php

function metal_prices_shortcode($atts) {
    $atts = shortcode_atts(
        array(
            'metal' => 'XAU',
            'period' => '1y',
        ),
        $atts,
        'metal_prices'
    );

    // Fetch the current price and historical data from the API
    $metal = strtoupper($atts['metal']);
    $period = $atts['period'];

    $current_data = file_get_contents("http://127.0.0.1:8000/api/metal/{$metal}/current");
    $history = file_get_contents("http://127.0.0.1:8000/api/metal/{$metal}/history?interval={$period}");

    $current_data = json_decode($current_data, true);
    $history = json_decode($history, true);

    $current_price = $current_data['current'];
    $previous_price = $current_data['previous'];

    // Calculate the variation
    if ($previous_price) {
        $price_change = $current_price['price'] - $previous_price['price'];
        $percentage_change = ($price_change / $previous_price['price']) * 100;
    } else {
        $price_change = 0;
        $percentage_change = 0;
    }

    $change_class = $price_change >= 0 ? 'positive-change' : 'negative-change';
    $percentage_change_formatted = ($price_change >= 0 ? '+' : '') . number_format($percentage_change, 2);

    // Enqueue Chart.js and the date adapter
    wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js', array(), null, true);
    wp_enqueue_script('chartjs-adapter-date-fns', 'https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns', array('chartjs'), null, true);
    wp_enqueue_script('jquery');

    // Get the real name and color of the metal
    $metal_name = get_metal_name($metal);
    $metal_color = get_metal_color($metal);

    ob_start();
    ?>

    <div class="text-white-cloud bg-green-primary" data-gsap>
        <div class="container mx-auto">
            <div data-anim data-position="0.25" data-from='{"x": -100,"autoAlpha":0}' data-to='{"x": 0,"autoAlpha":1,"duration": 1.25}'>
                <h2 id="current-price-title">Prix courant de <?php echo esc_html($metal_name); ?>: <?php echo esc_html($current_price['price']); ?> €</h2>
                <span id="price-change" class="<?php echo esc_attr($change_class); ?>">
                    (<?php echo $price_change >= 0 ? '+' : ''; ?><?php echo number_format($price_change, 2); ?> €, <?php echo $percentage_change_formatted; ?>%)
                </span>
            </div>
            <div class="metal-prices" data-anim data-position="0.75" data-from='{"y": 100,"autoAlpha":0}' data-to='{"y": 0,"autoAlpha":1,"duration": 1.25}'>

                <div class="controls">
                    <select id="metal-selector">
                        <option value="XAU" <?php echo $metal == 'XAU' ? 'selected' : ''; ?>>Or</option>
                        <option value="XAG" <?php echo $metal == 'XAG' ? 'selected' : ''; ?>>Argent</option>
                        <option value="XPT" <?php echo $metal == 'XPT' ? 'selected' : ''; ?>>Platine</option>
                        <option value="XPD" <?php echo $metal == 'XPD' ? 'selected' : ''; ?>>Palladium</option>
                    </select>
                    <select id="period-selector">
                        <option value="1d" <?php echo $period == '1d' ? 'selected' : ''; ?>>1 jour</option>
                        <option value="1w" <?php echo $period == '1w' ? 'selected' : ''; ?>>1 semaine</option>
                        <option value="1m" <?php echo $period == '1m' ? 'selected' : ''; ?>>1 mois</option>
                        <option value="1y" <?php echo $period == '1y' ? 'selected' : ''; ?>>1 année</option>
                    </select>
                </div>

                <canvas id="metalPricesChart"></canvas>
            </div>
        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        function fetchAndRenderChart(metal, period) {
            $.ajax({
                url: `http://127.0.0.1:8000/api/metal/${metal}/history?interval=${period}`,
                method: 'GET',
                success: function(data) {
                    var metalName = getMetalName(metal);
                    var metalColor = getMetalColor(metal);

                    $('#current-price-title').text(`Prix courant ${metalName}: ${data[data.length - 1].price} €`);

                    var ctx = document.getElementById('metalPricesChart').getContext('2d');
                    var labels = data.map(function(entry) { return entry.timestamp; });
                    var prices = data.map(function(entry) { return entry.price; });

                    if (window.metalChart) {
                        window.metalChart.destroy();
                    }

                    var xAxisOptions = {
                        type: 'time',
                        time: {
                            unit: 'day',
                            tooltipFormat: 'dd/MM/yyyy',
                            displayFormats: {
                                day: 'd MMM yyyy',
                            }
                        },
                        ticks: {
                            color: '#ffffff' // Color for x-axis labels
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)' // Light gray grid lines
                        }
                    };

                    if (period === '1y') {
                        xAxisOptions.time.unit = 'month';
                        xAxisOptions.time.displayFormats = {
                            month: 'MMM'
                        };
                        xAxisOptions.time.stepSize = 1;
                    } else if (period === '1d') {
                        xAxisOptions.time.unit = 'hour';
                        xAxisOptions.time.displayFormats = {
                            hour: 'HH:mm'
                        };
                    }

                    window.metalChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: `Prix ${metalName} (€) `,
                                data: prices,
                                borderColor: metalColor,
                                borderWidth: 2,
                                borderDash: [],
                                pointBackgroundColor: metalColor,
                                pointBorderColor: metalColor,
                                pointRadius: 3,
                                pointHoverRadius: 7,
                                backgroundColor: metalColor,
                                fill: false
                            }]
                        },
                        options: {
                            scales: {
                                x: xAxisOptions,
                                y: {
                                    ticks: {
                                        color: '#ffffff' // Color for y-axis labels
                                    },
                                    grid: {
                                        color: 'rgba(255, 255, 255, 0.1)' // Light gray grid lines
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    labels: {
                                        color: '#ffffff' // Color for legend labels
                                    }
                                }
                            },
                        }
                    });
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error fetching data: ', textStatus, errorThrown);
                }
            });

            // Fetch the current and previous prices
            $.ajax({
                url: `http://127.0.0.1:8000/api/metal/${metal}/current`,
                method: 'GET',
                success: function(data) {
                    var currentPrice = data.current.price;
                    var previousPrice = data.previous ? data.previous.price : currentPrice; // Handle the case where there's no previous price

                    var priceChange = currentPrice - previousPrice;
                    var percentageChange = (priceChange / previousPrice) * 100;

                    var changeClass = priceChange >= 0 ? 'positive-change' : 'negative-change';
                    var priceChangeFormatted = (priceChange >= 0 ? '+' : '') + priceChange.toFixed(2);
                    var percentageChangeFormatted = (priceChange >= 0 ? '+' : '') + percentageChange.toFixed(2) + '%';

                    $('#price-change').text(`(${priceChangeFormatted} €, ${percentageChangeFormatted})`).attr('class', changeClass);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error fetching current price: ', textStatus, errorThrown);
                }
            });
        }

        function getMetalName(code) {
            var metals = {
                'XAU': 'Or',
                'XAG': 'Argent',
                'XPT': 'Platine',
                'XPD': 'Palladium'
            };

            return metals[code] || code;
        }

        function getMetalColor(code) {
            var colors = {
                'XAU': 'rgb(196, 171, 95)',
                'XAG': 'silver',
                'XPT': 'gray',
                'XPD': 'darkgray'
            };

            return colors[code] || 'black';
        }

        $('#metal-selector').on('change', function() {
            var metal = $(this).val();
            var period = $('#period-selector').val();
            fetchAndRenderChart(metal, period);
        });

        $('#period-selector').on('change', function() {
            var period = $(this).val();
            var metal = $('#metal-selector').val();
            fetchAndRenderChart(metal, period);
        });

        // Initial load
        fetchAndRenderChart('<?php echo esc_js($metal); ?>', '<?php echo esc_js($period); ?>');
    });
    </script>

    <?php
    return ob_get_clean();
}

add_shortcode('metal_prices', 'metal_prices_shortcode');

// Fonction de mapping pour obtenir le nom du métal
function get_metal_name($code) {
    $metals = [
        'XAU' => 'Or',
        'XAG' => 'Argent',
        'XPT' => 'Platine',
        'XPD' => 'Palladium',
    ];

    return isset($metals[$code]) ? $metals[$code] : $code;
}

// Fonction de mapping pour obtenir la couleur du métal
function get_metal_color($code) {
    $colors = [
        'XAU' => 'rgb(196, 171, 95)',
        'XAG' => 'silver',
        'XPT' => 'gray',
        'XPD' => 'darkgray',
    ];

    return isset($colors[$code]) ? $colors[$code] : 'black';
}
