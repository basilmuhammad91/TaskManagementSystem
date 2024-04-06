const routes = [
    {
        name: 'dashboard', path: '/dashboard', component: require('./components/dashboard.vue').default
    },
    {
        name: 'roles', path: '/roles', component: require('./components/roles.vue').default
    },
    {
        name: 'permissions', path: '/permissions', component: require('./components/permissions.vue').default
    },
    {
        name: 'users', path: '/users', component: require('./components/users.vue').default
    },
    {
        name: 'tasks', path: '/tasks', component: require('./components/tasks.vue').default
    },
    {
        name: 'settings', path: '/settings', component: require('./components/settings.vue').default
    }
];
export default routes;