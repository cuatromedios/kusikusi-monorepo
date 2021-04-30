import { RouteConfig } from 'vue-router'

const routes: RouteConfig[] = [
  {
    path: '/',
    component: () => import('layouts/ExternalLayout.vue'),
    redirect: { name: 'login' },
    children: [
      {
        path: '/login',
        component: () => import('pages/Login.vue'),
        name: 'login'
      }
    ]
  },
  {
    path: '/panel',
    component: () => import('layouts/InternalLayout.vue'),
    children: [
      {
        path: '/content/:entity_id?/:model?/:conector?/:parent_entity_id?',
        component: () => import('pages/Content.vue'),
        name: 'content'
      },
      {
        path: '/media',
        component: () => import('pages/Media.vue'),
        name: 'media'
      }
    ]
  }
]

// Always leave this as last one
if (process.env.MODE !== 'ssr') {
  routes.push({
    path: '*',
    component: () => import('pages/Error404.vue')
  })
}

export default routes
