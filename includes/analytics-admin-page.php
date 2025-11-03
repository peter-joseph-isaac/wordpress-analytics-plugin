<?php

class AAD_Admin_Page {
    public function __construct() {
        add_action('admin_menu', [$this, 'register_page']);
    }

    public function register_page() {
        add_menu_page(
            'Analytics Dashboard',
            'Analytics',
            'read',
            'aad-dashboard',
            [$this, 'render_page'],
            'dashicons-chart-bar',
            60
        );
    }

    public function render_page() {
        ?>
        <div class="wrap">
            <h1>Analytics Dashboard</h1>
            <p>Top viewed posts on your site.</p>

            <div id="aadSummary" style="margin-bottom: 25px;">
                <strong>Total Views:</strong> <span id="aadTotalViews">0</span><br>
                <strong>Most Viewed Post:</strong> <span id="aadTopPost">N/A</span>
            </div>

            <canvas id="aadChart" width="600" height="300"></canvas>
            <div id="aadMessage" style="margin-top:15px; font-weight:bold;"></div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
        (function() {
            const apiUrl = "<?php echo esc_url(rest_url('aad/v1/stats')); ?>";
            const msgBox = document.getElementById('aadMessage');
            const totalViewsEl = document.getElementById('aadTotalViews');
            const topPostEl = document.getElementById('aadTopPost');

            async function loadChart() {
                try {
                    const res = await fetch(apiUrl, { credentials: 'same-origin' });
                    const data = await res.json();

                    if (!data.labels || data.labels.length === 0 || data.error) {
                        msgBox.textContent = "No data available. Visit some posts first!";
                        return;
                    }

                    const totalViews = data.views.reduce((sum, val) => sum + val, 0);
                    const maxViews = Math.max(...data.views);
                    const topPost = data.labels[data.views.indexOf(maxViews)];

                    totalViewsEl.textContent = totalViews;
                    topPostEl.textContent = topPost + " (" + maxViews + " views)";

                    const ctx = document.getElementById('aadChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Post Views',
                                data: data.views,
                                backgroundColor: data.labels.map((label, index) => {
                                    return data.views[index] === maxViews ? '#2271b1' : '#a0c4ff';
                                }),
                                borderRadius: 8
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { display: false },
                                title: { display: true, text: 'Top Viewed Posts' },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.parsed.y + ' views';
                                        }
                                    }
                                }
                            },
                            scales: { y: { beginAtZero: true } }
                        }
                    });

                } catch (err) {
                    msgBox.textContent = "Error loading chart data.";
                    console.error(err);
                }
            }

            loadChart();
        })();
        </script>
        <?php
    }
}