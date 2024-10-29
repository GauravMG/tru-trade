function loadClientAccountsDonutChart(donutData) {
    var donutChartCanvas = $('#donutChartClientAccounts').get(0).getContext('2d')
    var donutOptions = {
        maintainAspectRatio: false,
        responsive: true,
    }
    //Create pie or douhnut chart
    // You can switch between pie and douhnut using the method below.
    new Chart(donutChartCanvas, {
        type: 'doughnut',
        data: donutData,
        options: donutOptions
    })
}

function loadEarningsDonutChart(donutData) {
    var donutChartCanvas = $('#donutChartEarnings').get(0).getContext('2d')
    var donutOptions = {
        maintainAspectRatio: false,
        responsive: true,
    }
    //Create pie or douhnut chart
    // You can switch between pie and douhnut using the method below.
    new Chart(donutChartCanvas, {
        type: 'doughnut',
        data: donutData,
        options: donutOptions
    })
}

function loadEarningsBarChart(dataBarChart) {
    var barChartCanvas = $('#barChartEarnings').get(0).getContext('2d')
    var barChartData = $.extend(true, {}, dataBarChart)
    barChartData.datasets[0] = dataBarChart.datasets[0]

    var barChartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        datasetFill: false
    }

    new Chart(barChartCanvas, {
        type: 'bar',
        data: barChartData,
        options: barChartOptions
    })
}