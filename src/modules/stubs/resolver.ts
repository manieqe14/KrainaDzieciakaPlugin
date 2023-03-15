import { ResponseComposition, RestContext, RestRequest } from 'msw';
import * as orders from './mocks/orders.json'

export default function resolver(_req: RestRequest, res: ResponseComposition, ctx: RestContext){
    return res(ctx.status(200), ctx.json(orders["default"] ));
}