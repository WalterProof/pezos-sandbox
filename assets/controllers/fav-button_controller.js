import { Controller } from 'stimulus';
import Store from 'store';
import { addClass, removeClass } from '../util/class';

export default class extends Controller {
    static targets = ['button'];
    static values = {
        url: String,
        name: String,
    };

    prefix = 'fav_';

    connect() {
        if (undefined !== this.storeGet(this.nameValue)) {
            addClass(this.buttonTarget, 'active');
        }
    }

    toggle(e) {
        if (undefined !== this.storeGet(this.nameValue)) {
            this.storeRemove(this.nameValue);
            removeClass(this.buttonTarget, 'active');
        } else {
            this.storeSet(this.nameValue, this.urlValue);
            addClass(this.buttonTarget, 'active');
        }
    }

    storeRemove(key) {
        Store.remove(this.prefix + key);
    }

    storeSet(key, value) {
        Store.set(this.prefix + key, value);
    }

    storeGet(key) {
        return Store.get(this.prefix + key);
    }
}
