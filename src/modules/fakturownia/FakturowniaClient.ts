import { FakturowniaClientDeps } from "./FakturowniaClient.types";

export default class FakturowniaClient {
    private token: string | undefined;

    constructor(deps: FakturowniaClientDeps) {
        this.token = deps.client.generalSettings?.fakturownia?.token;
    }

    get settings(){
        return {
            token: this.token
        }
    }
}