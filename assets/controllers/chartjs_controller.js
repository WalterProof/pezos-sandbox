import { Controller } from 'stimulus';
import { useDispatch } from 'stimulus-use';
import { addClass, hasClass, removeClass } from '../util/class';

export default class extends Controller {
    static values = {
        diffUrl: String,
        interval: Number,
        title: String,
    };

    connect() {
        useDispatch(this);
    }

    onChartConnect(event) {
        const isDark = hasClass(document.documentElement, 'dark');
        document
            .querySelectorAll('.spinner')
            .forEach((spinner) => addClass(spinner, 'hidden'));
        this.chart = event.detail.chart;
        if (isDark) {
            this.chart.options.scales.xAxes[0].ticks.fontColor = '#fff';
            this.chart.options.scales.yAxes[0].ticks.fontColor = '#fff';
            this.chart.update();
        }
        setInterval(() => this.setNewData(), this.intervalValue);
    }

    async setNewData() {
        const resp = await fetch(this.diffUrlValue);
        const diff = await resp.json();

        if (diff.length === 0) {
            return;
        }

        const headLabels = this.chart.data.labels.slice(
            this.chart.data.labels.length - 10,
            this.chart.data.labels.length
        );

        diff.forEach((item) => {
            if (undefined === headLabels.find((el) => el === item.datetime)) {
                this.chart.data.labels.push(item.datetime);

                if (this.titleValue === 'Prices Dynamics') {
                    this.chart.data.datasets[0].data.push(item.ratio);
                    this.dispatch('updated');
                }

                if (this.titleValue === 'Pool Dynamics') {
                    this.chart.data.datasets[0].data.push(item.tez_pool);
                    this.chart.data.datasets[1].data.push(item.token_pool);
                }
            }
        });

        this.chart.update();
    }
}
