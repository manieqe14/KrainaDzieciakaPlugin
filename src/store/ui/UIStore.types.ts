import { Pages } from './uiConstans'

export type Page = { title: string, path: string };
export type CurrentPage = keyof typeof Pages;