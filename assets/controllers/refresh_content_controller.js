import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = ['content'];
    static values = {
        url: String,
        interval: Number,
    };

    connect() {
        setInterval(() => this.refreshContent(), this.intervalValue);
    }

    async refreshContent() {
        const response = await fetch(this.urlValue);
        this.contentTarget.innerHTML = await response.text();
    }
}
