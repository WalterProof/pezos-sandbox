import { Controller } from 'stimulus';
import Store from 'store';
import $ from 'jquery';

export default class extends Controller {
    static targets = ['list'];
    static values = {
        url: String,
    };

    prefix = 'fav_';

    connect() {
        const list = this.storeGetAll();
        if (Object.keys(list).length > 0) {
            $(this.listTarget).children('a').first().remove();
            for (const [k, v] of Object.entries(list)) {
                $(this.listTarget).append(
                    `<a href="#" class="block px-8 py-3 text-gray-900 no-underline bg-white border-t dark:text-gray-100 dark:hover:bg-gray-600 hover:bg-gray-300 dark:bg-gray-700 whitespace-nowrap" onclick="window.location.href = '${v}'">${k}</a>`
                );
            }
        }
    }

    storeSet(name, url) {
        Store.set(name, url);
        setStoreKeys(mapStoreKeys());
    }

    storeGetAll() {
        let list = [];
        Store.each((v, k) => {
            if (-1 !== k.indexOf(this.prefix)) {
                list[k.slice(this.prefix.length)] = v;
            }
        });
        return list;
    }
}
