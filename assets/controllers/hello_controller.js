import { Controller } from 'stimulus';
import { DAppClient } from '@airgap/beacon-sdk';

export default class extends Controller {
  dAppClient = null;

  async connect() {
    this.dAppClient = new DAppClient({ name: 'Pezos Sandbox' });
    const activeAccount = await dAppClient.getActiveAccount();

    if (activeAccount) {
      // You can now do an operation request, sign request, or send another permission request to switch wallet
      this.element.innerHTML = 'Already connected:' + activeAccount.address;
    } else {
      this.element.innerHTML = 'Not connected!';
    }
  }

  async requestPermissions() {
    const permissions = this.dAppClient.requestPermissions();
    console.log('New connection:', permissions.address);
    return permissions;
  }
}
