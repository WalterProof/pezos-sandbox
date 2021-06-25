import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = ['interval', 'timeIntervalForm'];

    connect() {
        this.intervalTarget.addEventListener('change', () =>
            this.timeIntervalFormTarget.submit()
        );
    }
}
