import { ResponseComposition, RestContext, RestRequest } from 'msw';

export const resolver = (model: object) =>
    (_req: RestRequest, res: ResponseComposition, ctx: RestContext) => res(ctx.status(200), ctx.json(model))