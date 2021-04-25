import { Controller } from 'stimulus';
import { useDispatch } from 'stimulus-use';
import { BeaconEvent, DAppClient } from '@airgap/beacon-sdk';

export default class extends Controller {
  static targets = ['activeAccount'];

  dAppClient = null;

  async connect() {
    useDispatch(this);
    this.dAppClient = new DAppClient({ name: 'Pezos Sandbox' });
    this.dAppClient.subscribeToEvent(BeaconEvent.PAIR_SUCCESS, (data) => {
      console.log(`${BeaconEvent.PAIR_SUCCESS} triggered: `, data);
    });
    const activeAccount = await this.dAppClient.getActiveAccount();

    if (activeAccount) {
      this.activeAccountTarget.innerHTML =
        'Already connected:' + activeAccount.address;
    } else {
      this.activeAccountTarget.innerHTML = 'Not connected!';
      this.requestPermissions();
    }
  }

  async requestPermissions() {
    const permissions = this.dAppClient.requestPermissions();
    console.log('New connection:', permissions.address);
    return permissions;
  }

  async clearActiveAccount() {
    await this.dAppClient.clearActiveAccount();
  }

  async requestSignPayload() {
    const response = await dAppClient.requestSignPayload({
      signingType: SigningType.RAW,
      payload: 'any string that will be signed',
    });

    console.log(`Signature: ${response.signature}`);
  }

  toggle(event) {
    console.log(event);
  }
}
