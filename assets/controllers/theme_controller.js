import { Controller } from 'stimulus';
import { addClass, hasClass, removeClass } from '../util/class';

export default class extends Controller {
    static targets = ['button'];

    toggle() {
        const isDark = hasClass(document.documentElement, 'dark');
        if (isDark) {
            localStorage.theme = 'light';
            removeClass(document.documentElement, 'dark');
        } else {
            localStorage.theme = 'dark';
            addClass(document.documentElement, 'dark');
        }
    }
}
