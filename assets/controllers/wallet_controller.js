import {Controller} from 'stimulus';
import {BeaconEvent, DAppClient, SigningType} from '@airgap/beacon-sdk';
import axios from 'axios';

export default class extends Controller {
    static targets = ['activeAccount', 'requestAccessButton'];
    static values = {
        requestAccessUrl: String,
        isAuthenticated: Boolean,
    };

    activeAccount = null;
    dAppClient = null;

    async initialize() {
        this.dAppClient = new DAppClient({name: 'Pezos Sandbox'});
        this.dAppClient.subscribeToEvent(BeaconEvent.PAIR_SUCCESS, async () => {
            await this.updateAccount();
        });
        await this.updateAccount();
    }

    async updateAccount() {
        this.activeAccount = await this.dAppClient.getActiveAccount();
        const {address} = {address: null, ...this.activeAccount};

        const activeAddress = address || 'no wallet connected.';
        const title = address ? 'Disconnect wallet' : 'Connect wallet';
        this.activeAccountTarget.innerHTML = `<span class="me-1">${activeAddress}</span><button class="btn btn-default btn-sm" data-action="wallet#toggle" title="${title}"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-wallet" viewBox="0 0 16 16">
  <path d="M0 3a2 2 0 0 1 2-2h13.5a.5.5 0 0 1 0 1H15v2a1 1 0 0 1 1 1v8.5a1.5 1.5 0 0 1-1.5 1.5h-12A2.5 2.5 0 0 1 0 12.5V3zm1 1.732V12.5A1.5 1.5 0 0 0 2.5 14h12a.5.5 0 0 0 .5-.5V5H2a1.99 1.99 0 0 1-1-.268zM1 3a1 1 0 0 0 1 1h12V2H2a1 1 0 0 0-1 1z"/>
</svg></button>`;
    }

    async toggle() {
        (await (this.dAppClient.getActiveAccount() && this.clearActiveAccount())) ||
            this.requestPermissions();
    }

    async requestPermissions() {
        await this.dAppClient.requestPermissions();
        await this.updateAccount();
    }

    async clearActiveAccount() {
        await this.dAppClient.clearActiveAccount();
        await this.updateAccount();
    }

    async requestAccess() {
        try {
            const {publicKey} = this.activeAccount;
            const payload = this.generatePayload();

            const {signature} = await this.dAppClient.requestSignPayload({
                signingType: SigningType.RAW,
                payload: payload,
                publicKey: publicKey,
            });

            await axios({
                method: 'post',
                url: this.requestAccessUrlValue,
                data: {payload, publicKey, signature},
            });

            // window.reload();
        } catch (error) {
            console.log(error);
        }
    }

    generatePayload(length = 32) {
        var result = [];
        var characters =
            'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var charactersLength = characters.length;
        for (var i = 0; i < length; i++) {
            result.push(
                characters.charAt(Math.floor(Math.random() * charactersLength))
            );
        }

        return result.join('');
    }
}
