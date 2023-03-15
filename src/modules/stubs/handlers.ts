import { rest } from 'msw';
import { WP_ACTIONS } from '../constants';
import resolver from './resolver';

export default [
    rest.post(`${WP_ACTIONS.AJAX_URL}?action=${WP_ACTIONS.FETCH_ORDERS}`, resolver),
    rest.get('/test', resolver)
]