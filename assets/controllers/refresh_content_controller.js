import { Controller } from 'stimulus';
import { addClass, removeClass } from '../util/class';

export default class extends Controller {
    static targets = ['content'];
    static values = {
        url: String,
        interval: Number,
        useEffect: Boolean,
    };

    connect() {
        setInterval(() => this.refreshContent(), this.intervalValue);
    }

    async refreshContent() {
        if (this.useEffectValue) {
            addClass(this.contentTarget, 'blink');
            setTimeout(() => removeClass(this.contentTarget, 'blink'), 2000);
        }
        const response = await fetch(this.urlValue);
        this.contentTarget.innerHTML = await response.text();
    }
}
