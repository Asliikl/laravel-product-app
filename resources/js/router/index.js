import { createRouter, createWebHistory } from "vue-router";

const routes = [
    {
        path: '/products',
        name: 'products.index',
        component: () => import('../components/products/index.vue')
    },
    {
        path: '/products/create',
        name: 'products.create',
        component: () => import('../components/products/Form.vue')
    },
    {
        path: '/products/:id/edit',
        name: 'products.edit',
        component: () => import('../components/products/Form.vue')
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
