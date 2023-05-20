$(document).ready(function () {
  document.getElementById('chartExpensesGenerals').onclick = function (evt) {
    var activePoints = chartExpensesGenerals.getElementsAtEvent(evt);

    if (activePoints.length > 0) {
      //get the internal index of slice in pie chart
      var clickedElementindex = activePoints[0]['_index'];

      //get specific label by index
      var label = chart.data.labels[clickedElementindex];

      //get value by index
      var value = chart.data.datasets[0].data[clickedElementindex];

      /* other stuff that requires slice's label and value */
    }
  };
});
