<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="row g-4 mb-4">
    <!-- Total Members -->
    <div class="col-lg-3 col-sm-6">
        <div class="card admin-card p-3 bg-white text-dark">
            <div class="d-flex align-items-center">
                <span class="fs-1 text-gold me-3"><i class="fa-solid fa-users"></i></span>
                <div>
                    <h6 class="text-muted mb-1 small text-uppercase" style="letter-spacing: 1px;">Anggota Aktif</h6>
                    <h3 class="fw-bold mb-0"><?= $totalMembers ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Products -->
    <div class="col-lg-3 col-sm-6">
        <div class="card admin-card p-3 bg-white text-dark">
            <div class="d-flex align-items-center">
                <span class="fs-1 text-gold me-3"><i class="fa-solid fa-mug-hot"></i></span>
                <div>
                    <h6 class="text-muted mb-1 small text-uppercase" style="letter-spacing: 1px;">Produk Kopi</h6>
                    <h3 class="fw-bold mb-0"><?= $totalProducts ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Total News -->
    <div class="col-lg-3 col-sm-6">
        <div class="card admin-card p-3 bg-white text-dark">
            <div class="d-flex align-items-center">
                <span class="fs-1 text-gold me-3"><i class="fa-solid fa-newspaper"></i></span>
                <div>
                    <h6 class="text-muted mb-1 small text-uppercase" style="letter-spacing: 1px;">Berita Terbit</h6>
                    <h3 class="fw-bold mb-0"><?= $totalNews ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Events -->
    <div class="col-lg-3 col-sm-6">
        <div class="card admin-card p-3 bg-white text-dark">
            <div class="d-flex align-items-center">
                <span class="fs-1 text-gold me-3"><i class="fa-solid fa-calendar-days"></i></span>
                <div>
                    <h6 class="text-muted mb-1 small text-uppercase" style="letter-spacing: 1px;">Total Event</h6>
                    <h3 class="fw-bold mb-0"><?= $totalEvents ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Komposisi Anggota Chart Row -->
<div class="row g-4 mb-4">
    <!-- Chart Column -->
    <div class="col-lg-8">
        <div class="card admin-card border-0 p-4 bg-white shadow-sm">
            <h5 class="vintage-serif mb-4 pb-2 border-bottom"><i class="fa-solid fa-chart-pie me-2 text-gold"></i>Komposisi Tingkatan Anggota</h5>
            <div style="height: 300px; position: relative;">
                <canvas id="memberCompositionChart"></canvas>
            </div>
        </div>
    </div>
    <!-- Detailed Stats Column -->
    <div class="col-lg-4">
        <div class="card admin-card border-0 p-4 bg-white shadow-sm h-100">
            <h5 class="vintage-serif mb-4 pb-2 border-bottom"><i class="fa-solid fa-circle-info me-2 text-gold"></i>Detail Status</h5>
            <div class="d-flex flex-column justify-content-around h-75 py-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fs-6 fw-semibold"><i class="fa-solid fa-circle me-2" style="color: #cda45e;"></i> Dewasa</span>
                    <span class="badge bg-gold px-3 py-2 fs-6 rounded-pill text-dark"><?= $statusCounts['Dewasa'] ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fs-6 fw-semibold"><i class="fa-solid fa-circle me-2" style="color: #8c6239;"></i> Madya</span>
                    <span class="badge text-white px-3 py-2 fs-6 rounded-pill" style="background-color: #8c6239;"><?= $statusCounts['Madya'] ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fs-6 fw-semibold"><i class="fa-solid fa-circle me-2" style="color: #5c3d2e;"></i> Utama</span>
                    <span class="badge text-white px-3 py-2 fs-6 rounded-pill" style="background-color: #5c3d2e;"><?= $statusCounts['Utama'] ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Grafik Batang per Kelurahan Row -->
<div class="row g-4 mb-4">
    <!-- Detail Komposisi per Kelurahan -->
    <div class="col-lg-6">
        <div class="card admin-card border-0 p-4 bg-white shadow-sm h-100">
            <h5 class="vintage-serif mb-4 pb-2 border-bottom"><i class="fa-solid fa-chart-column me-2 text-gold"></i>Komposisi Anggota per Kelurahan</h5>
            <div style="height: 350px; position: relative;">
                <canvas id="kelurahanBarChart"></canvas>
            </div>
        </div>
    </div>
    <!-- Total Kumulatif per Kelurahan -->
    <div class="col-lg-6">
        <div class="card admin-card border-0 p-4 bg-white shadow-sm h-100">
            <h5 class="vintage-serif mb-4 pb-2 border-bottom"><i class="fa-solid fa-chart-bar me-2 text-gold"></i>Total Anggota per Kelurahan (Kumulatif)</h5>
            <div style="height: 350px; position: relative;">
                <canvas id="kelurahanCumulativeChart"></canvas>
            </div>
        </div>
</div>

<!-- Grafik Kumulatif per Usia & Pendapatan Row -->
<div class="row g-4 mb-4">
    <!-- Rentang Usia -->
    <div class="col-lg-6">
        <div class="card admin-card border-0 p-4 bg-white shadow-sm h-100">
            <h5 class="vintage-serif mb-4 pb-2 border-bottom"><i class="fa-solid fa-cake-candles me-2 text-gold"></i>Kumulatif Anggota per Rentang Usia</h5>
            <div style="height: 350px; position: relative;">
                <canvas id="usiaCumulativeChart"></canvas>
            </div>
        </div>
    </div>
    <!-- Rentang Pendapatan -->
    <div class="col-lg-6">
        <div class="card admin-card border-0 p-4 bg-white shadow-sm h-100">
            <h5 class="vintage-serif mb-4 pb-2 border-bottom"><i class="fa-solid fa-wallet me-2 text-gold"></i>Kumulatif Anggota per Rentang Pendapatan</h5>
            <div style="height: 350px; position: relative;">
                <canvas id="pendapatanCumulativeChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Grafik Kumulatif per Pendidikan & Tarbiyah Anak Row -->
<div class="row g-4 mb-4">
    <!-- Rentang Pendidikan -->
    <div class="col-lg-6">
        <div class="card admin-card border-0 p-4 bg-white shadow-sm h-100">
            <h5 class="vintage-serif mb-4 pb-2 border-bottom"><i class="fa-solid fa-graduation-cap me-2 text-gold"></i>Kumulatif Anggota per Pendidikan Terakhir</h5>
            <div style="height: 350px; position: relative;">
                <canvas id="pendidikanCumulativeChart"></canvas>
            </div>
        </div>
    </div>
    <!-- Status Tarbiyah Anak -->
    <div class="col-lg-6">
        <div class="card admin-card border-0 p-4 bg-white shadow-sm h-100">
            <h5 class="vintage-serif mb-4 pb-2 border-bottom"><i class="fa-solid fa-child me-2 text-gold"></i>Status Tarbiyah Anak (dari Anggota Dewasa/Madya/Utama)</h5>
            <div style="height: 350px; position: relative;">
                <canvas id="childTarbiyahChart"></canvas>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Doughnut Chart for Member Composition
        const ctxDoughnut = document.getElementById('memberCompositionChart').getContext('2d');
        new Chart(ctxDoughnut, {
            type: 'doughnut',
            data: {
                labels: ['Dewasa', 'Madya', 'Utama'],
                datasets: [{
                    data: [
                        <?= $statusCounts['Dewasa'] ?>,
                        <?= $statusCounts['Madya'] ?>,
                        <?= $statusCounts['Utama'] ?>
                    ],
                    backgroundColor: [
                        '#cda45e', // Gold
                        '#8c6239', // Brown
                        '#5c3d2e'  // Dark Chocolate
                    ],
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                family: 'Poppins',
                                size: 13,
                                weight: '500'
                            },
                            color: '#333333',
                            padding: 20
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                    label += context.parsed + ' anggota';
                                }
                                return label;
                            }
                        }
                    }
                },
                cutout: '65%'
            }
        });

        // Common Data for Kelurahan Charts
        const labels = <?= json_encode(array_column($kelurahanStats, 'kelurahan_name')) ?>;
        const dewasaData = <?= json_encode(array_map('intval', array_column($kelurahanStats, 'dewasa_count'))) ?>;
        const madyaData = <?= json_encode(array_map('intval', array_column($kelurahanStats, 'madya_count'))) ?>;
        const utamaData = <?= json_encode(array_map('intval', array_column($kelurahanStats, 'utama_count'))) ?>;
        const totalData = <?= json_encode(array_map('intval', array_column($kelurahanStats, 'total_count'))) ?>;

        // Bar Chart for Member Composition per Kelurahan
        const ctxBar = document.getElementById('kelurahanBarChart').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Dewasa',
                        data: dewasaData,
                        backgroundColor: '#cda45e',
                        borderColor: '#cda45e',
                        borderWidth: 1
                    },
                    {
                        label: 'Madya',
                        data: madyaData,
                        backgroundColor: '#8c6239',
                        borderColor: '#8c6239',
                        borderWidth: 1
                    },
                    {
                        label: 'Utama',
                        data: utamaData,
                        backgroundColor: '#5c3d2e',
                        borderColor: '#5c3d2e',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: 'Poppins',
                                size: 11
                            },
                            color: '#666'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f0f0f0'
                        },
                        ticks: {
                            precision: 0,
                            font: {
                                family: 'Poppins',
                                size: 11
                            },
                            color: '#666'
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                family: 'Poppins',
                                size: 12,
                                weight: '500'
                            },
                            color: '#333333',
                            padding: 15
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y + ' anggota';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });

        // Cumulative Bar Chart for Total Members per Kelurahan
        const ctxCumulative = document.getElementById('kelurahanCumulativeChart').getContext('2d');
        new Chart(ctxCumulative, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Total Anggota',
                        data: totalData,
                        backgroundColor: '#8c6239', // Premium Brown
                        borderColor: '#8c6239',
                        borderWidth: 1,
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: 'Poppins',
                                size: 11
                            },
                            color: '#666'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f0f0f0'
                        },
                        ticks: {
                            precision: 0,
                            font: {
                                family: 'Poppins',
                                size: 11
                            },
                            color: '#666'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y + ' anggota';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });

        // Cumulative Bar Chart for Members per Age Range
        const ageLabels = <?= json_encode(array_column($usiaStats, 'usia_group')) ?>;
        const ageData = <?= json_encode(array_map('intval', array_column($usiaStats, 'total_count'))) ?>;

        const ctxUsia = document.getElementById('usiaCumulativeChart').getContext('2d');
        new Chart(ctxUsia, {
            type: 'bar',
            data: {
                labels: ageLabels,
                datasets: [
                    {
                        label: 'Total Anggota',
                        data: ageData,
                        backgroundColor: '#cda45e', // Gold
                        borderColor: '#cda45e',
                        borderWidth: 1,
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: 'Poppins',
                                size: 11
                            },
                            color: '#666'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f0f0f0'
                        },
                        ticks: {
                            precision: 0,
                            font: {
                                family: 'Poppins',
                                size: 11
                            },
                            color: '#666'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y + ' anggota';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });

        // Cumulative Bar Chart for Members per Income Range
        const incomeLabels = <?= json_encode(array_column($pendapatanStats, 'pendapatan_group')) ?>;
        const incomeData = <?= json_encode(array_map('intval', array_column($pendapatanStats, 'total_count'))) ?>;

        const ctxIncome = document.getElementById('pendapatanCumulativeChart').getContext('2d');
        new Chart(ctxIncome, {
            type: 'bar',
            data: {
                labels: incomeLabels,
                datasets: [
                    {
                        label: 'Total Anggota',
                        data: incomeData,
                        backgroundColor: '#8c6239', // Premium Brown
                        borderColor: '#8c6239',
                        borderWidth: 1,
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: 'Poppins',
                                size: 10
                            },
                            color: '#666'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f0f0f0'
                        },
                        ticks: {
                            precision: 0,
                            font: {
                                family: 'Poppins',
                                size: 11
                            },
                            color: '#666'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y + ' anggota';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });

        // Cumulative Bar Chart for Members per Education Level
        const eduLabels = <?= json_encode(array_column($pendidikanStats, 'pendidikan_group')) ?>;
        const eduData = <?= json_encode(array_map('intval', array_column($pendidikanStats, 'total_count'))) ?>;

        const ctxEdu = document.getElementById('pendidikanCumulativeChart').getContext('2d');
        new Chart(ctxEdu, {
            type: 'bar',
            data: {
                labels: eduLabels,
                datasets: [
                    {
                        label: 'Total Anggota',
                        data: eduData,
                        backgroundColor: '#cda45e', // Gold
                        borderColor: '#cda45e',
                        borderWidth: 1,
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: 'Poppins',
                                size: 11
                            },
                            color: '#666'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f0f0f0'
                        },
                        ticks: {
                            precision: 0,
                            font: {
                                family: 'Poppins',
                                size: 11
                            },
                            color: '#666'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y + ' anggota';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });

        // Doughnut Chart for Child Tarbiyah Status
        const ctxChild = document.getElementById('childTarbiyahChart').getContext('2d');
        new Chart(ctxChild, {
            type: 'doughnut',
            data: {
                labels: ['Sudah Tarbiyah', 'Belum Tarbiyah'],
                datasets: [{
                    data: [
                        <?= $childTarbiyah['sudah'] ?>,
                        <?= $childTarbiyah['belum'] ?>
                    ],
                    backgroundColor: [
                        '#cda45e', // Gold
                        '#5c3d2e'  // Dark Chocolate
                    ],
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                family: 'Poppins',
                                size: 13,
                                weight: '500'
                            },
                            color: '#333333',
                            padding: 20
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                    label += context.parsed + ' anak';
                                }
                                return label;
                            }
                        }
                    }
                },
                cutout: '65%'
            }
        });
    });
</script>
<?= $this->endSection() ?>
