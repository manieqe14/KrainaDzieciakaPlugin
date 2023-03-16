import { ResponseComposition, rest, RestContext, RestRequest } from 'msw';
import { WP_ACTIONS } from '../constants';
import resolver from './resolver';

export default [
    rest.post(`${WP_ACTIONS.AJAX_URL}?action=${WP_ACTIONS.FETCH_ORDERS}`, resolver),
    rest.get(`${WP_ACTIONS.AJAX_URL}?action=${WP_ACTIONS.GENERAL_CONFIG}`, (_req: RestRequest, res: ResponseComposition, ctx: RestContext) => res(ctx.status(200), ctx.json({})))
]