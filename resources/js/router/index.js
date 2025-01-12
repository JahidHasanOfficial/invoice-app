import { createRouter, createWebHistory } from 'vue-router';
import invoiceIndex from '../components/invoice/index.vue';
import invoiceCreate from '../components/invoice/create.vue';
import notFound from '../components/notFound.vue';

const routes = [
    {
        path: '/',
        component: invoiceIndex,
    },

    {
        path: '/invoice/new',
        component: invoiceCreate,
    },
    {
        path: '/:pathMatch(.*)*',
        component: notFound,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;