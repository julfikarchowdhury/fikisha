<template>
    <!-- navigation -->
    <section class="container-fluid navigation" id="sticky-naviation">
        <nav
            class="navbar navbar-expand-lg center-nav transparent navbar-light px-3"
        >
            <div class="container flex-lg-row flex-nowrap align-items-center">
                <div class="navbar-brand w-15 p-0">
                    <router-link to="/">
                        <img
                            :src="settings.logo"
                            alt=""
                            style="
                                object-fit: contain;
                                max-width: 200px;
                                height: 40px;
                            "
                        />
                    </router-link>
                </div>
                <div
                    class="navbar-collapse offcanvas offcanvas-nav offcanvas-start text-bg-dark"
                    tabindex="-1"
                    id="offcanvasDarkNavbar"
                    aria-labelledby="offcanvasDarkNavbarLabel"
                >
                    <div class="offcanvas-header w-100">
                        <h3 class="text-white fs-30 mb-0">
                            {{ settings.name }}
                        </h3>
                        <button
                            type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="offcanvas"
                            aria-label="Close"
                        ></button>
                    </div>
                    <div
                        class="offcanvas-body ms-lg-auto d-inline-block flex-column h-100 w-100"
                    >
                        <ul class="navbar-nav justify-content-end">
                            <li
                                class="nav-item d-block d-lg-none"
                                v-if="authUserData"
                            >
                                <a class="nav-link" href="/dashboard">
                                    {{ $t("dashboard") }}
                                </a>
                            </li>
                            <li class="nav-item d-block d-lg-none" v-else>
                                <a class="nav-link" href="/login">{{
                                    $t("login")
                                }}</a>
                            </li>
                            <li
                                class="nav-item"
                                v-for="menu in header_menus"
                                :key="`header-menu-${menu.page_id}-${menu.url}`"
                            >
                                <a :href="menu.url" class="nav-link">
                                    {{ menu.label }}
                                </a>
                            </li>
                            <li
                                class="nav-item d-none d-lg-block"
                                v-if="authUserData"
                            >
                                <div class="btn-group lang-item">
                                    <a
                                        href="#"
                                        class="dropdown-toggle text-white text-decoration-none font-size-20"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false"
                                    >
                                        {{ authUserData.name }}
                                    </a>
                                    <ul class="dropdown-menu bg-dark">
                                        <li>
                                            <a
                                                class="dropdown-item"
                                                href="/dashboard"
                                            >
                                                {{ $t("dashboard") }}
                                            </a>
                                        </li>
                                        <li>
                                            <a
                                                class="dropdown-item"
                                                href="/shipper/logout"
                                            >
                                                {{ $t("logout") }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="nav-item d-none d-lg-block" v-else>
                                <div class="d-flex h-100 align-items-center">
                                    <a
                                        class="btn btn-sm animate-button font-size-20 login-btn"
                                        href="/login"
                                    >
                                        {{ $t("login") }}
                                    </a>
                                </div>
                            </li>
                            <li
                                class="nav-item d-block d-lg-none"
                                v-if="authUserData"
                            >
                                <a class="nav-link" href="/shipper/logout">
                                    {{ $t("logout") }}
                                </a>
                            </li>
                        </ul>
                        <div class="offcanvas-footer d-lg-none mt-3">
                            <div>
                                <a
                                    role="button"
                                    class="link-inverse text-white"
                                    >{{ settings.email }}</a
                                >
                                <br />{{ settings.phone }}<br />
                                <nav class="nav social social-white mt-4">
                                    <a
                                        v-for="social_link in social_links"
                                        :key="social_link.id"
                                        :href="social_link.link"
                                        :title="social_link.name"
                                    >
                                        <i
                                            class="text-white"
                                            :class="social_link.icon"
                                        ></i>
                                    </a>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="navbar-other w-100 d-flex d-lg-none d-lg-inline-block ms-auto"
                >
                    <ul class="navbar-nav flex-row align-items-center ms-auto">
                        <li class="nav-item d-lg-none">
                            <button
                                class="offcanvas-nav-btn"
                                type="button"
                                data-bs-toggle="offcanvas"
                                data-bs-target="#offcanvasDarkNavbar"
                                aria-controls="offcanvasDarkNavbar"
                            >
                                <i class="fa fa-bars text-white"></i>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </section>
    <!-- // navigation -->

    <RouterView />

    <footer class="container-fluid border-top">
        <div class="container">
            <div class="py-5 pb-4">
                <div class="row footer-services">
                    <div
                        class="col-6 col-lg-3 col-sm-6"
                        v-for="service in services"
                        :key="service.id"
                    >
                        <h5 class="font-weight-bold">{{ service.name }}</h5>
                        <ul class="list-unstyled">
                            <li
                                v-for="vehicleType in service.vehicle_type"
                                :key="vehicleType.id"
                            >
                                <a
                                    role="button"
                                    class="text-decoration-none text-dark"
                                >
                                    {{ vehicleType.name }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <footer class="container-fluid">
        <div class="container text-center">
            <hr />
            <div class="row pt-4">
                <div class="col-lg-4 text-start">
                    <router-link to="/">
                        <img :src="settings.logo" width="120" alt="Logo" />
                    </router-link>
                    <div class="mt-3">
                        {{ settings.about }}
                    </div>
                </div>
                <div class="col-lg-5">
                    <ul class="list-unstyled text-start footer-pages">
                        <li class="d-inline-block p-3 pt-2 pb-2 ps-0">
                            <router-link
                                :to="{ name: 'about_us' }"
                                class="text-decoration-none text-dark"
                            >
                                {{ $t("about_us") }}
                            </router-link>
                        </li>
                        <li class="d-inline-block p-3 pt-2 pb-2 ps-0">
                            <router-link
                                :to="{ name: 'booking' }"
                                class="text-decoration-none text-dark"
                            >
                                {{ $t("booking") }}
                            </router-link>
                        </li>
                        <li class="d-inline-block p-3 pt-2 pb-2 ps-0">
                            <router-link
                                :to="{ name: 'faq' }"
                                class="text-decoration-none text-dark"
                            >
                                {{ $t("faq") }}
                            </router-link>
                        </li>
                        <li class="d-inline-block p-3 pt-2 pb-2 ps-0">
                            <router-link
                                :to="{ name: 'contact' }"
                                class="text-decoration-none text-dark"
                            >
                                {{ $t("contact") }}
                            </router-link>
                        </li>
                        <li class="d-inline-block p-3 pt-2 pb-2 ps-0">
                            <router-link
                                :to="{ name: 'terms_conditions' }"
                                class="text-decoration-none text-dark"
                            >
                                {{ $t("terms_conditions") }}
                            </router-link>
                        </li>
                        <li class="d-inline-block p-3 pt-2 pb-2 ps-0">
                            <router-link
                                :to="{ name: 'privacy_policy' }"
                                class="text-decoration-none text-dark"
                            >
                                {{ $t("privacy_policy") }}
                            </router-link>
                        </li>
                        <li class="d-inline-block p-3 pt-2 ps-0">
                            <a
                                href="/register"
                                class="text-decoration-none text-dark"
                            >
                                {{ $t("sing_up") }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <ul class="list-unstyled d-flex">
                        <li
                            v-for="social_link in social_links"
                            :key="social_link.id"
                        >
                            <a
                                :href="social_link.link"
                                class="text-decoration-none text-capitalize text-dark p-2"
                                :title="social_link.name"
                                style="padding-left: 0px !important"
                            >
                                <i
                                    :class="social_link.icon"
                                    class="text-primary"
                                    style="font-size: 30px !important"
                                ></i>
                            </a>
                        </li>
                    </ul>
                    <h5
                        class="mb-2 text-start font-weight-bold"
                        style="font-size: 15px"
                    >
                        {{ $t("download") }} {{ settings.name }} {{ $t("app") }}
                    </h5>
                    <div class="text-start">
                        <div class="d-inline-block">
                            <a
                                :href="download_now.playstore_link"
                                target="_blank"
                            >
                                <img
                                    class="mt-2"
                                    src="../../images/google-play.png"
                                />
                            </a>
                        </div>
                        <div class="d-inline-block">
                            <a :href="download_now.ios_link" target="_blank">
                                <img
                                    class="ms-2 mt-2"
                                    src="../../images/app-store.png"
                                />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="pt-5">
            <p class="py-3 mb-0 text-center border-top">
                {{ settings.copyright }}
            </p>
        </div>
    </footer>
</template>
<script>
import axios from "axios";
import config from "../config";
export default {
    data() {
        return {
            settings: {},
            services: [],
            download_now: {},
            languages: [],
            social_links: [],
            header_menus: [],
            default_language: {},
            loadingIn: false,
            locale_icon: "",
            locale_name: "",
            locale_code: "",
            authUserData: "",
        };
    },
    mounted() {
        this.defaultData();
        this.locale_icon = localStorage.getItem("locale_icon");
        this.locale_name = localStorage.getItem("locale_name");
        this.locale_code = localStorage.getItem("locale_code");
        this.authUserData = window.Laravel.authUserData
            ? window.Laravel.authUserData
            : "";
    },
    methods: {
        changeLanguage(id) {
            this.loadingIn = true;
            axios
                .get(config.API_URL_ROOT + "v10/change/language/" + id, {
                    headers: {
                        Accept: "application/json",
                        apiKey: config.API_KEY,
                        ContentType: "application/json",
                    },
                })
                .then((res) => {
                    localStorage.setItem(
                        "locale_icon",
                        res.data.data.language.icon_class
                    );
                    localStorage.setItem(
                        "locale_name",
                        res.data.data.language.name
                    );
                    localStorage.setItem(
                        "locale_code",
                        res.data.data.language.code
                    );
                    this.$i18n.locale = res.data.data.language.code;

                    location.reload();
                });
        },
        defaultData() {
            this.loadingIn = true;
            axios
                .get(config.API_URL_ROOT + "v10/default/data", {
                    headers: {
                        Accept: "application/json",
                        apiKey: config.API_KEY,
                        ContentType: "application/json",
                    },
                })
                .then((res) => {
                    this.settings = res.data.data.default.settings;
                    this.services = res.data.data.default.services;
                    this.download_now = res.data.data.default.download_now;
                    this.languages = res.data.data.default.languages;
                    this.social_links = res.data.data.default.social_links;
                    this.header_menus = res.data.data.default.header_menu || [];
                    const languageData = res.data.data.default.languages;
                    languageData.map((item) => {
                        if (
                            res.data.data.default.settings.default_language ==
                            item.code
                        ) {
                            this.default_language = item;
                        }
                    });
                });
        },
    },
};
</script>
<style>
.active {
    color: var(--bs-primary) !important;
    transition: 0.5s;
}
#nprogress .bar {
    background: var(--bs-primary) !important;
    height: 4px;
    z-index: 9999;
}
</style>
