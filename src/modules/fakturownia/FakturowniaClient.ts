import { FakturowniaClientDeps } from "./FakturowniaClient.types";
import { GeneralSettings } from '../client/client.types';

export default class FakturowniaClient {
    settings?: GeneralSettings['fakturownia'];

    constructor(deps: FakturowniaClientDeps) {
        this.settings = deps.client.generalSettings?.fakturownia;
    }
}