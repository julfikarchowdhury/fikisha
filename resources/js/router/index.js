import { createRouter, createWebHistory } from "vue-router";
import { setPrivateHeaders } from "../service/axiosInstance.js";
import config from "../config.js";
import i18n from "../lang";
import NProgress from 'nprogress';
import 'nprogress/nprogress.css';
// Configure NProgress settings if you want
NProgress.configure({ showSpinner: false });

setPrivateHeaders();

const routes = [
    {
        path: "/",
        component: () => import("../layouts/default.vue"),
        children: [
            {
                path: "/",
                name: "home",
                component: () => import("../views/Home.vue"),
                meta: {
                    title: i18n.global.t("home"),
                },
            },
            {
                path: "/truck/service",
                name: "truck_service",
                component: () => import("../views/HireTruckService.vue"),
                meta: {
                    title: i18n.global.t("truck_service"),
                },
            },
            {
                path: "/tracking/log",
                name: "tracking_log",
                component: () => import("../views/TrackingLog.vue"),
                meta: {
                    title: i18n.global.t("track_ship"),
                },
            },
            {
                path: "/about_us",
                name: "about_us",
                component: () => import("../views/AboutUs.vue"),
                meta: {
                    title: i18n.global.t("about_us"),
                },
            },
            {
                path: "/get_qoute",
                name: "get_qoute",
                component: () => import("../views/Getqoute.vue"),
                meta: {
                    title: i18n.global.t("get_qoute"),
                },
            },
            {
                path: "/faq",
                name: "faq",
                component: () => import("../views/Faq.vue"),
                meta: {
                    title: i18n.global.t("faq"),
                },
            },
            {
                path: "/contact",
                name: "contact",
                component: () => import("../views/Contact.vue"),
                meta: {
                    title: i18n.global.t("contact"),
                },
            },
            {
                path: "/terms/conditions",
                name: "terms_conditions",
                component: () => import("../views/TermsConditions.vue"),
                meta: {
                    title: i18n.global.t("terms_conditions"),
                },
            },
            {
                path: "/privacy/policy",
                name: "privacy_policy",
                component: () => import("../views/PrivacyPolicy.vue"),
                meta: {
                    title: i18n.global.t("privacy_policy"),
                },
            },
            {
                path: "/network/coverage",
                name: "network_coverage",
                component: () => import("../views/NetworkCoverage.vue"),
                meta: {
                    title: i18n.global.t("network_coverage"),
                },
            },
            {
                path: "/track/ship",
                name: "track_ship",
                component: () => import("../views/TrackShip.vue"),
                meta: {
                    title: i18n.global.t("track_ship"),
                },
            },
        ],
    }
];

const router = createRouter({
    history: createWebHistory('/'),
    linkActiveClass: "active",
    linkExactActiveClass: "active",
    scrollBehavior(to, from, savedPosition) {
        return new Promise((resolve) => {
            if (to.hash) {
                resolve({ selector: to.hash });
            } else if (savedPosition) {
                resolve(savedPosition);
            } else {
                resolve({
                    left: 0,
                    top: 0,
                    x: 0,
                    y: 0
                });
            }
        });
    },
    routes,
});

const DEFAULT_TITLE = config.SITE_TITLE;
router.beforeEach((to, from, next) => {
    const title = to.meta.title;
    if (title) {
        document.title = title + ' | ' + DEFAULT_TITLE;
    } else {
        document.title = DEFAULT_TITLE;
    }
    NProgress.start();
    next();
});

router.afterEach(() => {
    NProgress.done();
});

export default router;
