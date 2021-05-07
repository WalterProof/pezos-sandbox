import { Controller } from 'stimulus';
import { BeaconEvent, DAppClient, SigningType } from '@airgap/beacon-sdk';
import axios from 'axios';
import $ from 'jquery';
import shortenAddress from '../util/shortenAddress';

export default class extends Controller {
  static targets = ['activeAccount', 'loginForm', 'signupForm'];

  activeAccount = null;
  dAppClient = null;

  async initialize() {
    this.dAppClient = new DAppClient({ name: 'Pezos Sandbox' });
    this.dAppClient.subscribeToEvent(BeaconEvent.PAIR_SUCCESS, async () => {
      await this.updateAccount();
    });
    await this.updateAccount();
  }

  async updateAccount() {
    this.activeAccount = await this.dAppClient.getActiveAccount();
    const { address, publicKey } = {
      address: null,
      publicKey: null,
      ...this.activeAccount,
    };

    this.activeAccountTarget.innerHTML = address
      ? `<span class="me-1">${shortenAddress(
          address
        )}</span><button class="btn btn-primary btn-sm" data-action="wallet#toggle">Disconnect</button>`
      : '<button class="btn btn-primary btn-sm" data-action="wallet#toggle">Connect wallet</button>';

    const $loginFormUsernameField = $(this.loginFormTarget).find('#_username');
    const $signupFormPubKeyField = $(this.signupFormTarget).find(
      '#signup_form_pubKey'
    );
    const $signupFormSignatureField = $(this.signupFormTarget).find(
      '#signup_form_signature'
    );

    $loginFormUsernameField.val(address);
    $signupFormPubKeyField.val(publicKey);

    $loginFormUsernameField.attr('readonly', address !== null);
    $signupFormPubKeyField.attr('readonly', publicKey !== null);
    $signupFormSignatureField.attr('readonly', publicKey !== null);

    publicKey && $signupFormSignatureField.parent('.input-group').length === 0
      ? $signupFormSignatureField
          .wrap('<div class="input-group"></div>')
          .after(
            $(
              '<div class="input-group-append"> <button class="btn btn-outline-secondary" data-action="wallet#requestSignPayload">Sign with wallet</button></div>'
            )
          )
      : $signupFormSignatureField
          .unwrap('.input-group')
          .next('.input-group-append')
          .remove();
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

  async requestSignPayload(event) {
    event.preventDefault();

    try {
      const { publicKey } = this.activeAccount;
      const payload = $(this.signupFormTarget)
        .find('#signup_form_password_first')
        .val();

      const { signature } = await this.dAppClient.requestSignPayload({
        signingType: SigningType.RAW,
        payload: payload,
        publicKey: publicKey,
      });

      $(this.signupFormTarget).find('#signup_form_signature').val(signature);
    } catch (error) {
      console.log(error);
    }
  }
}
