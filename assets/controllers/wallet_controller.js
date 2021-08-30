import { Controller } from 'stimulus';
import { hasClass } from '../util/class';
import {
    ColorMode,
    DAppClient,
    PermissionScope,
    SigningType,
} from '@airgap/beacon-sdk';
import $ from 'jquery';

export default class extends Controller {
    static targets = ['loginForm'];

    activeAccount = null;
    dAppClient = null;

    async initialize() {
        this.dAppClient = new DAppClient({
            name: 'Pezos Sandbox',
            colorMode: this.isDark() ? ColorMode.DARK : ColorMode.LIGHT,
        });
    }

    async login() {
        const { publicKey, msg, signedPayload } = await this.signLoginRequest();

        $(this.loginFormTarget).find('#msg').val(msg);
        $(this.loginFormTarget).find('#sig').val(signedPayload);
        $(this.loginFormTarget).find('#pubKey').val(publicKey);
        $(this.loginFormTarget).submit();
    }

    async signLoginRequest() {
        await this.dAppClient.requestPermissions({
            scopes: [PermissionScope.SIGN],
        });
        const acct = await this.dAppClient.getActiveAccount();

        return await this.signMessage(
            JSON.stringify({
                type: 'auth',
                name: 'Pezos Sandbox',
                pkh: await acct.address,
                nonce: Math.random() * 100000000000000000,
            }),
            acct
        );
    }

    async signMessage(msg, acct) {
        const { address, publicKey } = acct;

        msg = 'Tezos Signed Message: ' + msg;
        const input = Buffer.from(msg);
        const prefix = Buffer.from('0501', 'hex');
        const len_bytes = Buffer.from(
            msg.length.toString(16).padStart(8, '0'),
            'hex'
        );
        msg = Buffer.concat(
            [prefix, len_bytes, input],
            prefix.length + len_bytes.length + input.length
        );

        // Bytes to hex
        msg = msg.toString('hex');

        let signedPayload = false;
        try {
            signedPayload = (
                await this.dAppClient.requestSignPayload({
                    SigningType: SigningType.MICHELINE,
                    payload: msg,
                    sourceAddress: address,
                })
            ).signature;
        } catch (signPayloadError) {
            console.error(signPayloadError);
        }

        return { publicKey, msg, signedPayload };
    }

    isDark() {
        return hasClass(document.documentElement, 'dark');
    }
}
