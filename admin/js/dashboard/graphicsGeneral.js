$(document).ready(function () {
    // Usuarios Activos
    graphicActualUsers = (data) => {
        let users = [];
        let quantity = [];
        let total = 0;

        let date = actualDate();

        data = data.filter(item => item.session_active == 1 && item.format_date == date);
                
        data = Object.values(data.reduce((result, currentItem) => {
            const userID = currentItem.id_user;
                 
            if (!result[userID]) {
                result[userID] = {
                    id_user: currentItem.id_user,
                    firstname: currentItem.firstname,
                    lastname: currentItem.lastname,
                    id_company: currentItem.id_company,
                    company: currentItem.company,
                    count: 1,
                };
            } else {
                result[userID].count++;
            }
                
            return result;
        }, {}));
                
        for (let i in data) {
            users.push(`${data[i].firstname} ${data[i].lastname}`);
            quantity.push(data[i].count);
            total = total + data[i].count;
        }
                
        $('#totalActualUsers').html(`${total.toLocaleString('es-CO', { maximumFractionDigits: 0 })}`);
                 
        cmo = document.getElementById('chartActualUsers');
        chartActualUsers = new Chart(cmo, {
            plugins: [ChartDataLabels],
            type: 'doughnut',
            data: {
                labels: users,
                datasets: [
                    {
                        data: quantity,
                        backgroundColor: getRandomColor(data.length),
                        borderWidth: 1,
                    },
                ],
            },
            options: {
                plugins: {
                    legend: {
                        display: false,
                    },
                    datalabels: {
                        formatter: (value, ctx) => {
                            let sum = 0;
                            let dataArr = ctx.chart.data.datasets[0].data;
                            dataArr.map((data) => {
                                sum += data;
                            });
                
                            let percentage = (value * 100) / sum;
                            if (percentage > 3)
                                return `${percentage.toLocaleString('es-CO', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2,
                                })} %`;
                            else return '';
                        },
                        color: 'white',
                        font: {
                            size: '14',
                            weight: 'bold',
                        },
                    },
                },
            },
        });
    };

    // Empresas
    graphicCompanies = (data) => {
        let companies = [];
        let quantity = [];
        let total = 0;

        data = Object.values(data.reduce((result, currentItem) => {
            const companyId = currentItem.id_company;

            // Si el grupo aún no existe, créalo
            if (!result[companyId]) {
                result[companyId] = {
                    id_user: currentItem.id_user,
                    firstname: currentItem.firstname,
                    lastname: currentItem.lastname,
                    id_company: currentItem.id_company,
                    company: currentItem.company,
                    count: 1,
                };
            } else {
                // Incrementa el contador si el grupo ya existe
                result[companyId].count++;
            }

            return result;
        }, {}));

        for (let i in data) {
            companies.push(data[i].company);
            quantity.push(data[i].count);
            total = total + data[i].count;
        }

        if (quantity.length > 1) {
            let maxDataValue = Math.max(...quantity);
            let minDataValue = Math.min(...quantity);
            let valueRange = maxDataValue - minDataValue;

            let step = Math.ceil(valueRange / 10 / 10) * 10;

            maxYValue = Math.ceil(maxDataValue / step) * step + step;
        } else {
            maxYValue = Math.max(...quantity);
        }

        $('#totalComapnies').html(`${total.toLocaleString('es-CO', { maximumFractionDigits: 0 })}`);
 
        cmo = document.getElementById('chartCompanies');
        chartCompanies = new Chart(cmo, {
            plugins: [ChartDataLabels],
            type: 'bar',
            data: {
                labels: companies,
                datasets: [
                    {
                        data: quantity,
                        backgroundColor: getRandomColor(data.length),
                        borderWidth: 1,
                    },
                ],
            },
            options: {
                onClick: function (e) {
                    let elements = chartCompanies.getElementsAtEventForMode(
                        e,
                        "nearest",
                        { intersect: true },
                        true
                    );

                    if (elements && elements.length > 0) {
                        let activeElement = elements[0];

                        let dataIndex = activeElement.index;
                        let label = chartCompanies.data.labels[dataIndex];
                        

                        // sad(label);
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: maxYValue,
                    },
                    x: {
                        display: false,
                    },
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        offset: 2,
                        formatter: (value, ctx) => {
                            let sum = 0;
                            let dataArr = ctx.chart.data.datasets[0].data;
                            dataArr.map((data) => {
                                sum += data;
                            });
                            let percentage = (value * 100) / sum;
                            isNaN(percentage) ? (percentage = 0) : percentage;
                            return `${percentage.toLocaleString('es-CO', {
                                maximumFractionDigits: 2,
                            })} %`;
                        },
                        color: 'black',
                        font: {
                            size: '10',
                            weight: 'light',
                        },
                    },
                },
            },
        });
    };

    // Usuarios
    graphicUsers = (data) => {
        let users = [];
        let quantity = [];
        let total = 0;

        data = Object.values(data.reduce((result, currentItem) => {
            const userID = currentItem.id_user;
 
            if (!result[userID]) {
                result[userID] = {
                    id_user: currentItem.id_user,
                    firstname: currentItem.firstname,
                    lastname: currentItem.lastname,
                    id_company: currentItem.id_company,
                    company: currentItem.company,
                    count: 1,
                };
            } else {
                result[userID].count++;
            }

            return result;
        }, {}));

        for (let i in data) {
            users.push(`${data[i].firstname} ${data[i].lastname}`);
            quantity.push(data[i].count);
            total = total + data[i].count;
        }

        if (quantity.length > 1) {
            let maxDataValue = Math.max(...quantity);
            let minDataValue = Math.min(...quantity);
            let valueRange = maxDataValue - minDataValue;

            let step = Math.ceil(valueRange / 10 / 10) * 10;

            maxYValue = Math.ceil(maxDataValue / step) * step + step;
        } else {
            maxYValue = Math.max(...quantity);
        }

        $('#totalUsers').html(`${total.toLocaleString('es-CO', { maximumFractionDigits: 0 })}`);
        
        cmo = document.getElementById('chartUsers');
        chartUsers = new Chart(cmo, {
            plugins: [ChartDataLabels],
            type: 'bar',
            data: {
                labels: users,
                datasets: [
                    {
                        data: quantity,
                        backgroundColor: getRandomColor(data.length),
                        borderWidth: 1,
                    },
                ],
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: maxYValue,
                    },
                    x: {
                        display: false,
                    },
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        offset: 2,
                        formatter: (value, ctx) => {
                            let sum = 0;
                            let dataArr = ctx.chart.data.datasets[0].data;
                            dataArr.map((data) => {
                                sum += data;
                            });
                            let percentage = (value * 100) / sum;
                            isNaN(percentage) ? (percentage = 0) : percentage;
                            return `${percentage.toLocaleString('es-CO', {
                                maximumFractionDigits: 2,
                            })} %`;
                        },
                        color: 'black',
                        font: {
                            size: '10',
                            weight: 'light',
                        },
                    },
                },
            },
        });
    };

    // Mes
    graphicMonth = (data) => {
        let date = [];
        let quantity = [];
        let total = 0;

        data = Object.values(data.reduce((result, currentItem) => {
            const date = currentItem.format_date;

            // Si el grupo aún no existe, créalo
            if (!result[date]) {
                result[date] = {
                    id_user: currentItem.id_user,
                    firstname: currentItem.firstname,
                    lastname: currentItem.lastname,
                    id_company: currentItem.id_company,
                    company: currentItem.company,
                    day: currentItem.day,
                    month: currentItem.month,
                    count: 1,
                };
            } else {
                // Incrementa el contador si el grupo ya existe
                result[date].count++;
            }

            return result;
        }, {}));

        for (let i in data) {
            date.push(`${data[i].day} de ${data[i].month}`);
            quantity.push(data[i].count);
            total = total + data[i].count;
        }

        // $('#totalMonth').html(`${total.toLocaleString('es-CO', { maximumFractionDigits: 0 })}`);

        cmo = document.getElementById('chartMonth');
        chartMonth = new Chart(cmo, {
            plugins: [ChartDataLabels],
            type: 'bar',
            data: {
                labels: date,
                datasets: [
                    {
                        data: quantity,
                        backgroundColor: getRandomColor(data.length),
                        borderWidth: 1,
                    },
                ],
            },
            options: {
                onClick: function (e) {
                    let elements = chartMonth.getElementsAtEventForMode(
                        e,
                        "nearest",
                        { intersect: true },
                        true
                    );

                    if (elements && elements.length > 0) {
                        let activeElement = elements[0];

                        let dataIndex = activeElement.index;
                        let label = chartMonth.data.labels[dataIndex];

                        // loadModalExpenses(label, data);
                    }
                }, 
                 scales: {
                    y: {
                        beginAtZero: true,
                        max: maxYValue,
                    },
                    x: {
                        display: false,
                    },
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        offset: 2,
                        formatter: (value, ctx) => {
                            let sum = 0;
                            let dataArr = ctx.chart.data.datasets[0].data;
                            dataArr.map((data) => {
                                sum += data;
                            });
                            let percentage = (value * 100) / sum;
                            isNaN(percentage) ? (percentage = 0) : percentage;
                            return `${percentage.toLocaleString('es-CO', {
                                maximumFractionDigits: 2,
                            })} %`;
                        },
                        color: 'black',
                        font: {
                            size: '10',
                            weight: 'light',
                        },
                    },
                },
            },
        });
    };

    // Año
    graphicYear = (data) => {
        let month = [];
        let quantity = [];
        let total = 0;

        data = Object.values(data.reduce((result, currentItem) => {
            const month = currentItem.month;

            // Si el grupo aún no existe, créalo
            if (!result[month]) {
                result[month] = {
                    id_user: currentItem.id_user,
                    firstname: currentItem.firstname,
                    lastname: currentItem.lastname,
                    id_company: currentItem.id_company,
                    company: currentItem.company,
                    day: currentItem.day,
                    month: currentItem.month,
                    count: 1,
                };
            } else {
                // Incrementa el contador si el grupo ya existe
                result[month].count++;
            }

            return result;
        }, {}));

        for (let i in data) {
            month.push(data[i].month);
            quantity.push(data[i].count);
            total = total + data[i].count;
        }

        $('#totalYear').html(`${total.toLocaleString('es-CO', { maximumFractionDigits: 0 })}`);

        cmo = document.getElementById('chartYear');
        chartYear = new Chart(cmo, {
            plugins: [ChartDataLabels],
            type: 'doughnut',
            data: {
                labels: month,
                datasets: [
                    {
                        data: quantity,
                        backgroundColor: getRandomColor(data.length),
                        borderWidth: 1,
                    },
                ],
            },
            options: {
                plugins: {
                    legend: {
                        display: false,
                    },
                    datalabels: {
                        formatter: (value, ctx) => {
                            let sum = 0;
                            let dataArr = ctx.chart.data.datasets[0].data;
                            dataArr.map((data) => {
                                sum += data;
                            });
                
                            let percentage = (value * 100) / sum;
                            if (percentage > 3)
                                return `${percentage.toLocaleString('es-CO', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2,
                                })} %`;
                            else return '';
                        },
                        color: 'white',
                        font: {
                            size: '14',
                            weight: 'bold',
                        },
                    },
                },
            },
        });
    };
});