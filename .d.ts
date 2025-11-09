import {route} from './vendor/tightenco/ziggy'

declare module 'vue' {
    interface ComponentCustomProperties {
        route: typeof route;
    }
}