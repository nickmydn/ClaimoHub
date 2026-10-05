import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  scrollBehavior(to, from, savedPosition) {
    return savedPosition || { left: 0, top: 0 }
  },
  routes: [
    {
      path: '/',
      name: 'Dashboard',
      component: () => import('../views/Dashboard.vue'),
      meta: {
        requiresAuth: true,
      },
    },
    {
      path: '/admin/distributors',
      name: 'Admin Distributors',
      component: () => import('../views/Admin/Distributor.vue'),
      meta: {
        title: 'Distributors',
        requiresAuth: true,
        roles: ['admin'],
      },
    },
    {
      path: '/admin/merchants',
      name: 'Admin Merchants',
      component: () => import('../views/Admin/Merchant.vue'),
      meta: {
        title: 'Merchants',
        requiresAuth: true,
        roles: ['admin'],
      },
    },
    {
      path: '/admin/products',
      name: 'Admin Products',
      component: () => import('../views/Admin/Product.vue'),
      meta: {
        title: 'Products',
        requiresAuth: true,
        roles: ['admin'],
      },
    },
    {
      path: '/admin/voucher-products',
      name: 'Admin Voucher Products',
      component: () => import('../views/Admin/VoucherProduct.vue'),
      meta: {
        title: 'Voucher Products',
        requiresAuth: true,
        roles: ['admin'],
      },
    },
    {
      path: '/admin/rewards',
      name: 'Admin Rewards',
      component: () => import('../views/Admin/Reward.vue'),
      meta: {
        title: 'Rewards',
        requiresAuth: true,
        roles: ['admin'],
      },
    },
    {
      path: '/customer/transactions',
      name: 'Customer Transactions',
      component: () => import('../views/Customer/Transactions.vue'),
      meta: {
        title: 'My Transactions',
        requiresAuth: true,
        roles: ['customer'],
      },
    },
    {
      path: '/customer/rewards',
      name: 'Customer Rewards',
      component: () => import('../views/Customer/Rewards.vue'),
      meta: {
        title: 'My Rewards',
        requiresAuth: true,
        roles: ['customer'],
      },
    },
    {
      path: '/calendar',
      name: 'Calendar',
      component: () => import('../views/Others/Calendar.vue'),
      meta: {
        title: 'Calendar',
        requiresAuth: true,
      },
    },
    {
      path: '/profile',
      name: 'Profile',
      component: () => import('../views/Others/UserProfile.vue'),
      meta: {
        title: 'Profile',
        requiresAuth: true,
      },
    },
    {
      path: '/form-elements',
      name: 'Form Elements',
      component: () => import('../views/Forms/FormElements.vue'),
      meta: {
        title: 'Form Elements',
        requiresAuth: true,
      },
    },
    {
      path: '/basic-tables',
      name: 'Basic Tables',
      component: () => import('../views/Tables/BasicTables.vue'),
      meta: {
        title: 'Basic Tables',
        requiresAuth: true,
      },
    },
    {
      path: '/line-chart',
      name: 'Line Chart',
      component: () => import('../views/Chart/LineChart/LineChart.vue'),
    },
    {
      path: '/bar-chart',
      name: 'Bar Chart',
      component: () => import('../views/Chart/BarChart/BarChart.vue'),
    },
    {
      path: '/alerts',
      name: 'Alerts',
      component: () => import('../views/UiElements/Alerts.vue'),
      meta: {
        title: 'Alerts',
        requiresAuth: true,
      },
    },
    {
      path: '/avatars',
      name: 'Avatars',
      component: () => import('../views/UiElements/Avatars.vue'),
      meta: {
        title: 'Avatars',
        requiresAuth: true,
      },
    },
    {
      path: '/badge',
      name: 'Badge',
      component: () => import('../views/UiElements/Badges.vue'),
      meta: {
        title: 'Badge',
        requiresAuth: true,
      },
    },

    {
      path: '/buttons',
      name: 'Buttons',
      component: () => import('../views/UiElements/Buttons.vue'),
      meta: {
        title: 'Buttons',
        requiresAuth: true,
      },
    },

    {
      path: '/images',
      name: 'Images',
      component: () => import('../views/UiElements/Images.vue'),
      meta: {
        title: 'Images',
        requiresAuth: true,
      },
    },
    {
      path: '/videos',
      name: 'Videos',
      component: () => import('../views/UiElements/Videos.vue'),
      meta: {
        title: 'Videos',
        requiresAuth: true,
      },
    },
    {
      path: '/blank',
      name: 'Blank',
      component: () => import('../views/Pages/BlankPage.vue'),
      meta: {
        title: 'Blank',
        requiresAuth: true,
      },
    },

    {
      path: '/error-404',
      name: '404 Error',
      component: () => import('../views/Errors/FourZeroFour.vue'),
      meta: {
        title: '404 Error',
      },
    },

    {
      path: '/signin',
      name: 'Signin',
      component: () => import('../views/Auth/Signin.vue'),
      meta: {
        title: 'Signin',
      },
    },
    {
      path: '/signup',
      name: 'Signup',
      component: () => import('../views/Auth/Signup.vue'),
      meta: {
        title: 'Signup',
      },
    },
    // {
    //   path: '/admin/distributors',
    //   name: 'Admin Distributors',
    //   component: () => import('../views/Admin/Distributor.vue'),
    //   meta: {
    //     title: 'Distributors',
    //     requiresAuth: true,
    //     roles: ['admin'],
    //   },
    // },
  ],
})

export default router

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()

  document.title = `${to.meta.title || 'ClaimoHub'} | ClaimoHub`

  const requiresAuth = to.meta.requiresAuth
  const allowedRoles = to.meta.roles as string[] | undefined

  // =========================
  // AUTHENTICATION
  // =========================

  if (requiresAuth && !authStore.isAuthenticated) {
    next({
      name: 'Signin',
      query: {
        redirect: to.fullPath,
      },
    })

    return
  }

  // =========================
  // ALREADY LOGIN
  // =========================

  if (
    authStore.isAuthenticated &&
    (to.name === 'Signin' || to.name === 'Signup')
  ) {
    next({
      name: 'Dashboard',
    })
    return
  }

  // =========================
  // ROLE AUTHORIZATION
  // =========================

  if (
    requiresAuth &&
    allowedRoles &&
    !allowedRoles.includes(authStore.role)
  ) {
    next({
      name: 'Dashboard',
    })
    return
  }

  next()
})
