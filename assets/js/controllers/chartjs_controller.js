import { Controller } from '@hotwired/stimulus';
import Chart from 'chart.js/auto';
import 'chartjs-adapter-moment';

export default class extends Controller {
    connect() {
        const payload = JSON.parse(this.element.getAttribute('data-view'));
        if (Array.isArray(payload.options) && 0 === payload.options.length) {
            payload.options = {};
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
}
