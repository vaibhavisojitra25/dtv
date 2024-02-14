@extends('layouts/contentLayoutMaster')

@section('title', 'Dashboard')

@section('vendor-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/rowGroup.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/animate/animate.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
    <!--chart-->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-sweet-alerts.css')) }}">
    <!-- chart -->
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/pickers/form-flat-pickr.css')) }}">
    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;

        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #e74c3c;
            -webkit-transition: .4s;
            transition: .4s;

        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked+.slider {
            background-color: #29c75f;

        }

        input:focus+.slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked+.slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }

        .on_off {
            margin-top: 10px;
        }

        .btn-sm {
            margin-right: 10px !important;
        }

        .table>tbody>tr>td {
            vertical-align: middle;
        }

        .btn-md {
            width: 200px;
            background-color: #fb2736 !important;
            color: white;
            font-size: 18px;
        }

    </style>
@endsection

@section('content')
    <!-- Dashboard Ecommerce Starts -->
    <section>
        <div class="row match-height">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body pb-50">
                        <h6> Total Credits</h6>
                        <h2 class="fw-bolder mb-1">{{ Auth::user()->credits }}</h2>
                        <div id="statistics-order-chart"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body pb-50">
                        <h6>Total Device</h6>
                        <h2 class="fw-bolder mb-1">{{ $diviceCount }}</h2>
                        <div id="statistics-order-chart"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body pb-50">
                        <h6> Total Playlist</h6>
                        <h2 class="fw-bolder mb-1">{{ $playlistCount }}</h2>
                        <div id="statistics-order-chart"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="chartjs-chart">
        <div class="row">
            <!--Bar Chart Start -->
            <div class="col-md-12">
                <div class="card">
                    <div
                        class="card-header d-flex justify-content-between align-items-sm-center align-items-start flex-sm-row flex-column ">
                        <div class="header-left">
                            <h4 class="card-title">Latest Device</h4>
                        </div>
                        {{-- <div class="header-right d-flex align-items-center mt-sm-0 mt-1">
                    <a href="{{route('users/list')}}"><button type="button" class="btn btn-light">Click on more</button></a>
                </div> --}}
                    </div>
                    <div class="card-body">
                        @if($device)
                        <canvas class="bar-chart-device chartjs" data-height="400"></canvas>
                        @else
                        <p>No data available for last 7 day
                        @endif
                    </div>
                </div>
            </div>
            <!-- Bar Chart End -->

        </div>
    </section>

@endsection

@section('vendor-script')
    {{-- Vendor js files --}}
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.bootstrap5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/responsive.bootstrap5.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/jszip.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/pdfmake.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/vfs_fonts.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.html5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.print.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.rowGroup.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/validation/additional-methods.min.js')) }}"></script>

    <script src="{{ asset(mix('vendors/js/forms/cleave/cleave.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/cleave/addons/cleave-phone.us.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
    <!-- chart -->
    <script src="{{ asset(mix('vendors/js/charts/chart.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>
@endsection

@section('page-script')
    <script src="{{ asset(mix('js/scripts/extensions/ext-component-sweet-alerts.js')) }}"></script>
    <!-- chart-->
    {{-- <script src="{{ asset(mix('js/scripts/charts/chart-chartjs.js')) }}"></script> --}}
    {{-- Page js files --}}
    <script>
        var tables;
        $(function() {
            tables = $('.datatables-ajax').DataTable({
                stateSave: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('dashboard') }}",
                    data: function(data) {
                        data.status = $("#status").val();
                    }
                },
                columns: [{
                        data: 'invoice_number',
                        name: 'invoice_number',
                        sClass: "align-middle"
                    },
                    {
                        data: 'profile',
                        name: 'profile',
                        orderable: false,
                        searchable: false,
                        sClass: "align-middle table-image"
                    },
                    {
                        data: 'plan_name',
                        name: 'plan_name',
                        sClass: "align-middle"
                    },
                    {
                        data: 'amount',
                        name: 'amount',
                        sClass: "align-middle"
                    },
                    {
                        data: 'expiry_date',
                        name: 'expiry_date',
                        sClass: "align-middle"
                    },
                    {
                        data: 'status',
                        name: 'status',
                        sClass: "align-middle"
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        sClass: "align-middle no-wrap"
                    },
                ],
                dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                orderCellsTop: true,
                language: {
                    search: 'Search',
                    searchPlaceholder: 'Search..',
                    paginate: {
                        // remove previous & next text from pagination
                        previous: '&nbsp;',
                        next: '&nbsp;'
                    }
                },
                "drawCallback": function(settings) {
                    feather.replace();
                }
            });

            $('#status').change(function() {
                $('.datatables-ajax').DataTable().ajax.reload(null, false);
            });

        });
    </script>

    <script>
        $(document).on('click', '.chkStatus', function(e) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var id = this.value;
            var closeInSeconds = 2;
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, change it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('users/change_user_status') }}/" + id,
                        type: "POST",
                        dataType: "json",
                        success: function(data) {
                            if (data.active) {
                                Swal.fire({
                                    icon: 'success',
                                    title: "Done!",
                                    text: "User Account Activated",
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                                location.reload()
                            } else {
                                Swal.fire({
                                    icon: 'success',
                                    title: "Done!",
                                    text: "User Account Suspended",
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                                location.reload()
                            }
                        }
                    });

                }
            })

        });

        function takeAccess(url, redirectTo, leaveUrl) {
            var tab;
            $.ajax({
                url: url,
                type: "GET",
                success: function(response) {
                    tab = window.open(redirectTo);
                    Swal.fire({
                        closeOnClickOutside: false,
                        title: "Access",
                        text: "You have taken an access of other user, you want to leave?",
                        icon: "warning",
                        buttons: {
                            cancel: false,
                            confirm: true,
                        }
                    }).then((willDelete) => {
                        if (willDelete) {
                            tab.close();
                            $.ajax({
                                url: leaveUrl,
                                type: "GET",
                                dataType: 'json',
                                success: function(response) {},
                                error: function(err) {}
                            });
                        }
                    });
                },
                error: function(err) {
                    if (err.responseJSON.message) {
                        toastr.error(err.responseJSON.message);
                    } else {
                        toastr.error('Something went wrong');
                    }
                }
            });
        }
    </script>
    <script>

        $(window).on('load', function() {
            'use strict';

            var chartWrapper = $('.chartjs'),
                flatPicker = $('.flat-picker'),
                barChartDevice = $('.bar-chart-device'),
                barChartUser = $('.bar-chart-user'),
                horizontalBarChartEx = $('.horizontal-bar-chart-ex'),
                lineChartEx = $('.line-chart-ex'),
                radarChartEx = $('.radar-chart-ex'),
                polarAreaChartEx = $('.polar-area-chart-ex'),
                bubbleChartEx = $('.bubble-chart-ex'),
                doughnutChartEx = $('.doughnut-chart-ex'),
                scatterChartEx = $('.scatter-chart-ex'),
                lineAreaChartEx = $('.line-area-chart-ex');

            // Color Variables
            var primaryColorShade = '#836AF9',
                yellowColor = '#ffe800',
                successColorShade = '#28dac6',
                warningColorShade = '#ffe802',
                warningLightColor = '#FDAC34',
                infoColorShade = '#299AFF',
                greyColor = '#4F5D70',
                blueColor = '#2c9aff',
                blueLightColor = '#84D0FF',
                greyLightColor = '#EDF1F4',
                tooltipShadow = 'rgba(0, 0, 0, 0.25)',
                lineChartPrimary = '#666ee8',
                lineChartDanger = '#ff4961',
                labelColor = '#6e6b7b',
                grid_line_color = 'rgba(200, 200, 200, 0.2)'; // RGBA color helps in dark layout

            // Detect Dark Layout
            if ($('html').hasClass('dark-layout')) {
                labelColor = '#b4b7bd';
            }

            // Wrap charts with div of height according to their data-height
            if (chartWrapper.length) {
                console.log(2);

                chartWrapper.each(function() {
                    $(this).wrap($('<div style="height:' + this.getAttribute('data-height') +
                    'px"></div>'));
                });
            }

            // Init flatpicker
            if (flatPicker.length) {
                var date = new Date();
                flatPicker.each(function() {
                    $(this).flatpickr({
                        mode: 'range',
                        defaultDate: ['2019-05-01', '2019-05-10']
                    });
                });
            }

            // Bar Chart
            // --------------------------------------------------------------------
            if (barChartDevice.length) {
                console.log(1);
                var barChartExample = new Chart(barChartDevice, {
                    type: 'bar',
                    options: {
                        elements: {
                            rectangle: {
                                borderWidth: 2,
                                borderSkipped: 'bottom'
                            }
                        },
                        responsive: true,
                        maintainAspectRatio: false,
                        responsiveAnimationDuration: 500,
                        legend: {
                            display: false
                        },
                        tooltips: {
                            // Updated default tooltip UI
                            shadowOffsetX: 1,
                            shadowOffsetY: 1,
                            shadowBlur: 8,
                            shadowColor: tooltipShadow,
                            backgroundColor: window.colors.solid.white,
                            titleFontColor: window.colors.solid.black,
                            bodyFontColor: window.colors.solid.black
                        },
                        scales: {
                            xAxes: [{
                                display: true,
                                gridLines: {
                                    display: true,
                                    color: grid_line_color,
                                    zeroLineColor: grid_line_color
                                },
                                scaleLabel: {
                                    display: false
                                },
                                ticks: {
                                    fontColor: labelColor
                                }
                            }],
                            yAxes: [{
                                display: true,
                                gridLines: {
                                    color: grid_line_color,
                                    zeroLineColor: grid_line_color
                                },
                                ticks: {
                                    stepSize: 2,
                                    min: 0,
                                    max: <?php echo $max; ?>,
                                    fontColor: labelColor
                                }
                            }]
                        }
                    },
                    data: {
                        labels: <?php echo $label; ?>,
                        datasets: [{
                            data: <?php echo $values; ?>,
                            barThickness: 15,
                            backgroundColor: successColorShade,
                            borderColor: 'transparent'
                        }]
                    }
                });
            }

            if (chartWrapper.length) {
                //Draw rectangle Bar charts with rounded border
                Chart.elements.Rectangle.prototype.draw = function() {
                    var ctx = this._chart.ctx;
                    var viewVar = this._view;
                    var left, right, top, bottom, signX, signY, borderSkipped, radius;
                    var borderWidth = viewVar.borderWidth;
                    var cornerRadius = 20;
                    if (!viewVar.horizontal) {
                        left = viewVar.x - viewVar.width / 2;
                        right = viewVar.x + viewVar.width / 2;
                        top = viewVar.y;
                        bottom = viewVar.base;
                        signX = 1;
                        signY = top > bottom ? 1 : -1;
                        borderSkipped = viewVar.borderSkipped || 'bottom';
                    } else {
                        left = viewVar.base;
                        right = viewVar.x;
                        top = viewVar.y - viewVar.height / 2;
                        bottom = viewVar.y + viewVar.height / 2;
                        signX = right > left ? 1 : -1;
                        signY = 1;
                        borderSkipped = viewVar.borderSkipped || 'left';
                    }

                    if (borderWidth) {
                        var barSize = Math.min(Math.abs(left - right), Math.abs(top - bottom));
                        borderWidth = borderWidth > barSize ? barSize : borderWidth;
                        var halfStroke = borderWidth / 2;
                        var borderLeft = left + (borderSkipped !== 'left' ? halfStroke * signX : 0);
                        var borderRight = right + (borderSkipped !== 'right' ? -halfStroke * signX : 0);
                        var borderTop = top + (borderSkipped !== 'top' ? halfStroke * signY : 0);
                        var borderBottom = bottom + (borderSkipped !== 'bottom' ? -halfStroke * signY : 0);
                        if (borderLeft !== borderRight) {
                            top = borderTop;
                            bottom = borderBottom;
                        }
                        if (borderTop !== borderBottom) {
                            left = borderLeft;
                            right = borderRight;
                        }
                    }

                    ctx.beginPath();
                    ctx.fillStyle = viewVar.backgroundColor;
                    ctx.strokeStyle = viewVar.borderColor;
                    ctx.lineWidth = borderWidth;
                    var corners = [
                        [left, bottom],
                        [left, top],
                        [right, top],
                        [right, bottom]
                    ];

                    var borders = ['bottom', 'left', 'top', 'right'];
                    var startCorner = borders.indexOf(borderSkipped, 0);
                    if (startCorner === -1) {
                        startCorner = 0;
                    }

                    function cornerAt(index) {
                        return corners[(startCorner + index) % 4];
                    }

                    var corner = cornerAt(0);
                    ctx.moveTo(corner[0], corner[1]);

                    for (var i = 1; i < 4; i++) {
                        corner = cornerAt(i);
                        var nextCornerId = i + 1;
                        if (nextCornerId == 4) {
                            nextCornerId = 0;
                        }

                        var nextCorner = cornerAt(nextCornerId);

                        var width = corners[2][0] - corners[1][0],
                            height = corners[0][1] - corners[1][1],
                            x = corners[1][0],
                            y = corners[1][1];

                        var radius = cornerRadius;

                        if (radius > height / 2) {
                            radius = height / 2;
                        }
                        if (radius > width / 2) {
                            radius = width / 2;
                        }

                        if (!viewVar.horizontal) {
                            ctx.moveTo(x + radius, y);
                            ctx.lineTo(x + width - radius, y);
                            ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
                            ctx.lineTo(x + width, y + height - radius);
                            ctx.quadraticCurveTo(x + width, y + height, x + width, y + height);
                            ctx.lineTo(x + radius, y + height);
                            ctx.quadraticCurveTo(x, y + height, x, y + height);
                            ctx.lineTo(x, y + radius);
                            ctx.quadraticCurveTo(x, y, x + radius, y);
                        } else {
                            ctx.moveTo(x + radius, y);
                            ctx.lineTo(x + width - radius, y);
                            ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
                            ctx.lineTo(x + width, y + height - radius);
                            ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
                            ctx.lineTo(x + radius, y + height);
                            ctx.quadraticCurveTo(x, y + height, x, y + height);
                            ctx.lineTo(x, y + radius);
                            ctx.quadraticCurveTo(x, y, x, y);
                        }
                    }

                    ctx.fill();
                    if (borderWidth) {
                        ctx.stroke();
                    }
                };
            }


        });
    </script>
@endsection
