import { Controller } from 'stimulus';
import { BeaconEvent, DAppClient, SigningType } from '@airgap/beacon-sdk';

export default class extends Controller {
  static targets = ['activeAccount'];

  dAppClient = null;

  async initialize() {
    this.dAppClient = new DAppClient({ name: 'Pezos Sandbox' });
    this.dAppClient.subscribeToEvent(BeaconEvent.PAIR_SUCCESS, async () => {
      await this.updateAccount();
    });
    await this.updateAccount();
  }

  async updateAccount() {
    const activeAccount = await this.dAppClient.getActiveAccount();
    this.activeAccountTarget.innerHTML = activeAccount
      ? `${activeAccount.address} <button data-action="wallet#toggle">Disconnect Wallet</button>`
      : `anon. <button data-action="wallet#toggle">Connect Wallet</button>`;
    console.log({ activeAccount });
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
    const response = await this.dAppClient.requestSignPayload({
      signingType: SigningType.RAW,
      payload: event.currentTarget.getAttribute('data-payload'),
    });

    console.log(`Signature: ${response.signature}`);
    console.log({ response });
  }
}
