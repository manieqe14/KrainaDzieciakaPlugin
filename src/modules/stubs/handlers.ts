import { WP_ACTIONS } from '../constants';
import { resolver } from './resolver';
import * as orders from './mocks/orders.json';
import * as generalSettings from './mocks/generalSettings.json';
import { rest } from 'msw';

export default [
    rest.post(`${WP_ACTIONS.AJAX_URL}?action=${WP_ACTIONS.FETCH_ORDERS}`, resolver(orders['default'])),
    rest.get(`${WP_ACTIONS.AJAX_URL}?action=${WP_ACTIONS.GENERAL_CONFIG}`, resolver(generalSettings['default']))
]