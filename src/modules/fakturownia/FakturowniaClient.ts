export default class FakturowniaClient {
    private token: string | undefined;

    constructor(token?: string) {
        this.token = token;
    }
}