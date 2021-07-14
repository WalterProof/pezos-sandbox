import { Controller } from 'stimulus';
import { addClass, hasClass, removeClass } from '../util/class';

export default class extends Controller {
    static targets = ['menu'];

    toggleMenu() {
        if (hasClass(this.menuTarget, 'hidden')) {
            removeClass(this.menuTarget, 'hidden');
        } else {
            addClass(this.menuTarget, 'hidden');
        }
    }
}
