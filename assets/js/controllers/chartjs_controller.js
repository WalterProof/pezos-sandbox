import { Controller } from '@hotwired/stimulus';
import Chart from 'chart.js/auto';
import 'chartjs-adapter-moment';

export default class extends Controller {
    connect() {
        const payload = JSON.parse(this.element.getAttribute('data-view'));
        if (Array.isArray(payload.options) && 0 === payload.options.length) {
            payload.options = {};
        }

        if (payload.options.scales.hasOwnProperty('tez')) {
            payload.options.scales.tez.ticks.callback = this.nFormat
        }

        if (payload.options.scales.hasOwnProperty('token')) {
            payload.options.scales.token.ticks.callback = this.nFormat
        }

        this._dispatchEvent('chartjs:pre-connect', {
            options: payload.options,
        });

        const chart = new Chart(this.element.getContext('2d'), payload);

        this._dispatchEvent('chartjs:connect', { chart });
    }

    _dispatchEvent(
        name,
        payload = null,
        canBubble = false,
        cancelable = false
    ) {
        const userEvent = document.createEvent('CustomEvent');
        userEvent.initCustomEvent(name, canBubble, cancelable, payload);

        this.element.dispatchEvent(userEvent);
    }

    nFormat(num) {
        const fmt = (v) => v.toFixed(3).replace(/0+$/, '').replace(/\.$/,'')

        if (num >= 1000000000) {
            return fmt(num / 1000000000) + 'B';
        }
        if (num >= 1000000) {
            return fmt(num / 1000000) + 'M';
        }
        if (num >= 1000) {
            return fmt(num / 1000) + 'K';
        }

        return num;
    }
}
