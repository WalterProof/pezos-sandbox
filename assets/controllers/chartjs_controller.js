import { Controller } from 'stimulus';

export default class extends Controller {
  onChartConnect(event) {
    this.chart = event.detail.chart;

    setTimeout(() => this.setNewData(), 5000);
  }

  setNewData() {
    this.chart.data.datasets[0].data[2] = 30;
    this.chart.update();
  }
}

// import { Controller } from 'stimulus';

// export default class extends Controller {
//     connect() {
//         this.element.addEventListener('chartjs:pre-connect', this._onPreConnect);
//         this.element.addEventListener('chartjs:connect', this._onConnect);
//     }

//     disconnect() {
//         // You should always remove listeners when the controller is disconnected to avoid side effects
//         this.element.removeEventListener('chartjs:pre-connect', this._onPreConnect);
//         this.element.removeEventListener('chartjs:connect', this._onConnect);
//     }

//     _onPreConnect(event) {
//         // The chart is not yet created
//         console.log(event.detail.options); // You can access the chart options using the event details

//         // For instance you can format Y axis
//         event.detail.options.scales = {
//             yAxes: [
//                 {
//                     ticks: {
//                         callback: function (value, index, values) {
//                             /* ... */
//                         },
//                     },
//                 },
//             ],
//         };
//     }

//     _onConnect(event) {
//         // The chart was just created
//         console.log(event.detail.chart); // You can access the chart instance using the event details

//         // For instance you can listen to additional events
//         event.detail.chart.options.onHover = (mouseEvent) => {
//             /* ... */
//         };
//         event.detail.chart.options.onClick = (mouseEvent) => {
//             /* ... */
//         };
//     }
// }
