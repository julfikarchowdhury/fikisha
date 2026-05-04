<template>
    <skeleton-loader height="100%" width="100%" :loading="loadingIn">
        <!-- Start Slider -->
        <!-- <Slider /> -->
        <SliderNew />
        <!-- End Slider -->
        <HireYourTruck />
        <!-- marketplace pricing -->
        <section class="container-fluid pt-0 mt-5 mt-lg-0 py-5 bg-gray-white">
            <div class="container py-5">
                <div class="row mt-5">
                    <div class="col-lg-6">
                        <h4 class="title text-uppercase mb-3 text-primary">
                            Marketplace Delivery Pricing
                        </h4>
                        <p class="text-muted mb-4">
                            Final delivery fee is calculated from distance, weight, and payer type.
                        </p>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h6 class="mb-3">{{ $t("inside_city") }}</h6>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Base Fare</span>
                                            <strong>{{ formatAmount(marketplacePricing.inside_city_base_fare) }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Per KM</span>
                                            <strong>{{ formatAmount(marketplacePricing.inside_city_per_km_rate) }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Per KG</span>
                                            <strong>{{ formatAmount(marketplacePricing.inside_city_per_kg_rate) }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h6 class="mb-3">{{ $t("outside_city") }}</h6>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Base Fare</span>
                                            <strong>{{ formatAmount(marketplacePricing.outside_city_base_fare) }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Per KM</span>
                                            <strong>{{ formatAmount(marketplacePricing.outside_city_per_km_rate) }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Per KG</span>
                                            <strong>{{ formatAmount(marketplacePricing.outside_city_per_kg_rate) }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between flex-wrap gap-2">
                                    <span>Inside City Threshold</span>
                                    <strong>{{ marketplacePricing.inside_city_distance ?? 0 }} km</strong>
                                </div>
                                <div class="d-flex justify-content-between flex-wrap gap-2 mt-2">
                                    <span>Receiver Pays Markup</span>
                                    <strong>{{ marketplacePricing.marketplace_receiver_markup_percent ?? 0 }}%</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div>
                            <img
                                class="w-100"
                                style="border-radius: 10px !important"
                                :src="service_banner.banner_image"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- //end marketplace pricing -->

        <!-- Move for us -->
        <section class="container-fluid py-5 pb-0 pt-0 why-choose-us">
            <div class="py-5 pb-0">
                <div class="row pt-0 align-items-center mb-3 why-choose-us-row">
                    <div class="col-lg-6 d-none d-lg-block m-0 p-0">
                        <div class="mt-0">
                            <img
                                style="width: 100%"
                                :src="why_choose_us.info.banner"
                                alt="Message"
                            />
                        </div>
                    </div>
                    <div class="col-lg-6 ps-5">
                        <h4 class="title text-uppercase my-4 mb-4 text-white">
                            {{ why_choose_us.info.title }}
                        </h4>

                        <div
                            class="d-flex mb-3 why-choose-content"
                            v-for="whychoose_item in why_choose_us.list"
                            :key="whychoose_item.id"
                        >
                            <div>
                                <i
                                    :class="whychoose_item.icon"
                                    class="me-2"
                                ></i>
                            </div>
                            <div>{{ whychoose_item.content }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Move for us -->
        <section class="container-fluid py-5 pt-5 bg-gray-white">
            <div class="container py-5">
                <h4 class="title text-uppercase mb-3 text-primary text-center">
                    {{ our_work_process.title }}
                </h4>
                <p class="text-center">{{ our_work_process.description }}</p>
                <div
                    class="row pt-2 align-items-center mt-5 our-work-process justify-content-between"
                >
                    <div class="col-2 text-center">
                        <div
                            class="process-item"
                            :style="{
                                background:
                                    'url(' + our_work_process.icon_1 + ')',
                                'background-repeat': 'no-repeat',
                                'background-color': '#fff',
                            }"
                        ></div>
                        <div>
                            {{ our_work_process.title_1 }}
                        </div>
                    </div>
                    <div class="col-2 text-center">
                        <div
                            class="process-item"
                            :style="{
                                background:
                                    'url(' + our_work_process.icon_2 + ')',
                                'background-repeat': 'no-repeat',
                                'background-color': '#fff',
                            }"
                        ></div>
                        <div>
                            {{ our_work_process.title_2 }}
                        </div>
                    </div>
                    <div class="col-2 text-center">
                        <div
                            class="process-item"
                            :style="{
                                background:
                                    'url(' + our_work_process.icon_3 + ')',
                                'background-repeat': 'no-repeat',
                                'background-color': '#fff',
                            }"
                        ></div>
                        <div>
                            {{ our_work_process.title_3 }}
                        </div>
                    </div>
                    <div class="col-2 text-center">
                        <div
                            class="process-item"
                            :style="{
                                background:
                                    'url(' + our_work_process.icon_4 + ')',
                                'background-repeat': 'no-repeat',
                                'background-color': '#fff',
                            }"
                        ></div>
                        <div>
                            {{ our_work_process.title_4 }}
                        </div>
                    </div>
                    <div class="col-2 text-center">
                        <div
                            class="process-item"
                            :style="{
                                background:
                                    'url(' + our_work_process.icon_5 + ')',
                                'background-repeat': 'no-repeat',
                                'background-color': '#fff',
                            }"
                        ></div>
                        <div>
                            {{ our_work_process.title_5 }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- our hero_section -->
        <section class="container-fluid pb-0 py-5">
            <div class="container py-5 faq">
                <h4 class="title text-uppercase mb-5 text-primary text-center">
                    {{ $t("faq_title") }}
                </h4>

                <div class="accordion" id="accordionShipper">
                    <div
                        v-for="(faq, i) in faq_list"
                        :key="faq.id"
                        class="accordion-item"
                    >
                        <h2 class="accordion-header" :id="`heading${faq.id}`">
                            <button
                                class="accordion-button fw-medium collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                :data-bs-target="`#collapse${faq.id}`"
                                :aria-expanded="i === 0"
                                :aria-controls="`collapse${faq.id}`"
                            >
                                {{ faq.question }}
                            </button>
                        </h2>
                        <div
                            :id="`collapse${faq.id}`"
                            class="accordion-collapse collapse"
                            :aria-labelledby="`heading${faq.id}`"
                            data-bs-parent="#accordionShipper"
                        >
                            <div class="accordion-body">
                                <Transition>
                                    <p v-html="faq.answer"></p>
                                </Transition>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- logo cloud -->
        <section class="container-fluid py-5 pt-0">
            <div class="container">
                <div class="bg-white py-24 sm:py-32">
                    <div class="mx-auto max-w-7xl px-6 lg:px-8">
                        <BrandSlider />
                    </div>
                </div>
            </div>
        </section>
        <!-- //end logo cloud -->

    </skeleton-loader>
</template>
<script>
import { onMounted, ref } from "vue";
import Slider from "@/components/Slider.vue";
import SliderNew from "@/components/SliderNew.vue";
import BrandSlider from "@/components/BrandSlider.vue";
import HireYourTruck from "@/components/HireYourTruck.vue";
import axios from "axios";
export default {
    setup() {
        const authUser = ref({});
        const loadingIn = ref(false);

        const SITE_TITLE = ref(config.SITE_TITLE);
        const SITE_TITLE_URL = ref(config.SITE_TITLE_URL);

        const service_banner = ref({});
        const faq_list = ref({});
        const marketplacePricing = ref({
            inside_city_distance: 0,
            inside_city_base_fare: 0,
            inside_city_per_km_rate: 0,
            inside_city_per_kg_rate: 0,
            outside_city_base_fare: 0,
            outside_city_per_km_rate: 0,
            outside_city_per_kg_rate: 0,
            marketplace_receiver_markup_percent: 0,
            currency: "",
            currency_location: "left",
        });
        const errors = ref(null);

        const our_work_process = ref({});
        const about = ref({});
        const subscribe = ref({});
        const app_link = ref({});
        const map_link = ref({});
        const why_choose_us = ref({});

        setInterval(
            function () {
                authUser.value = window.Laravel.authUser;
            }.bind(this),
            5000
        );

        const sectionHomeData = () => {
            loadingIn.value = true;
            axios
                .get(config.API_URL_ROOT + "v10/sections", {
                    headers: {
                        Accept: "application/json",
                        apiKey: config.API_KEY,
                        ContentType: "application/json",
                    },
                })
                .then((res) => {
                    service_banner.value =
                        res.data.data.sections.service_banner;
                    our_work_process.value =
                        res.data.data.sections.our_work_process;
                    why_choose_us.value = res.data.data.sections.why_choose_us;
                })
                .finally(() => {
                    loadingIn.value = false;
                });
        };

        const faqData = () => {
            loadingIn.value = true;
            axios
                .get(config.API_URL_ROOT + "v10/pages/faq", {
                    headers: {
                        Accept: "application/json",
                        apiKey: config.API_KEY,
                        ContentType: "application/json",
                    },
                })
                .then((res) => {
                    faq_list.value = res.data.data.faq_list;
                })
                .finally((e) => {
                    loadingIn.value = false;
                });
        };
        const pricingData = () => {
            loadingIn.value = true;
            axios
                .get(config.API_URL_ROOT + "v10/default/data", {
                    headers: {
                        Accept: "application/json",
                        apiKey: config.API_KEY,
                        ContentType: "application/json",
                    },
                })
                .then((res) => {
                    const settings = (res.data && res.data.data && res.data.data.default && res.data.data.default.settings)
                        ? res.data.data.default.settings
                        : {};
                    marketplacePricing.value = {
                        ...marketplacePricing.value,
                        ...settings,
                        currency: settings.currency || "",
                        currency_location: settings.currency_location || "left",
                    };
                })
                .finally((e) => {
                    loadingIn.value = false;
                });
        };

        function formatAmount(value) {
            const amount = Number(value || 0).toFixed(2);
            const currency = marketplacePricing.value.currency || "";
            const currencyLocation = (marketplacePricing.value.currency_location || "left").toLowerCase();
            if (!currency) {
                return amount;
            }
            return currencyLocation === "right" ? `${amount} ${currency}` : `${currency} ${amount}`;
        }

        onMounted(() => {
            sectionHomeData();
            faqData();
            pricingData();
            authUser.value = window.Laravel.authUser;
        });

        return {
            authUser,
            loadingIn,
            SITE_TITLE,
            SITE_TITLE_URL,
            service_banner,
            errors,
            faqData,
            faq_list,
            marketplacePricing,
            pricingData,
            formatAmount,

            our_work_process,
            about,
            subscribe,
            app_link,
            map_link,
            why_choose_us,
        };
    },
    components: {
        Slider,
        SliderNew,
        BrandSlider,
        HireYourTruck,
    },
};
</script>
<style>
.font-weight-bold {
    font-weight: bold;
}

.custom-map {
    width: 100%;
    border: 1px solid var(--bs-deep-primary) !important;
    border-radius: 5px;
}

ul.truck_type_ul {
    margin: 0;
    padding: 0;
    list-style: none;
}

ul.truck_type_ul > li {
    width: auto;
    display: inline-block;
}

.truck-icon-selector {
    position: relative;
    border: 1px solid #b5b5b5;
    border-radius: 4px;
    max-width: 120px;
    height: 125px;
    display: inline-block;
    cursor: pointer;
    margin-right: 20px;
    text-align: center;
    vertical-align: top;
}

.truck-icon-selector.active {
    background: #e7e7e7;
}

.truck-icon-selector img {
    vertical-align: middle;
    max-height: 80%;
    max-width: 90%;
    margin: 10px;
}

.truck-icon-selector small {
    margin-bottom: 10px;
}

ul.vehicle_ul {
    margin: 0;
    padding: 0;
    list-style: none;
}

ul.vehicle_ul > li {
    width: auto;
    display: inline-block;
}

ul.vehicle_ul > li .truck-icon-selector {
    position: relative;
    border: 1px solid #b5b5b5;
    border-radius: 4px;
    max-width: 120px;
    height: 125px;
    display: inline-block;
    cursor: pointer;
    margin-right: 20px;
    text-align: center;
    vertical-align: top;
}

ul.vehicle_ul > li .truck-icon-selector.active {
    background: #e7e7e7;
}

ul.vehicle_ul > li .truck-icon-selector img {
    vertical-align: middle;
    max-height: 80%;
    max-width: 85%;
    margin: 10px;
}

ul.vehicle_ul > li .truck-icon-selector small {
    margin-bottom: 10px;
}

.pac-container {
    z-index: 99999 !important;
}

.main-search-input-item {
    flex: 1;
    margin-top: 3px;
    position: relative;
}

#autocomplete-container,
#autocomplete-input {
    position: relative;
    z-index: 101;
}

.main-search-input input,
.main-search-input input:focus {
    font-size: 16px;
    border: none;
    background: #fff;
    margin: 0;
    padding: 0;
    height: 44px;
    line-height: 44px;
    box-shadow: none;
}

.input-with-icon i,
.main-search-input-item.location a {
    padding: 5px 10px;
    z-index: 101;
}

.main-search-input-item.location a {
    position: absolute;
    right: -50px;
    top: 40%;
    transform: translateY(-50%);
    color: #999;
    padding: 10px;
}

.current-location {
    margin-right: 50px;
    margin-top: 5px;
    color: var(--bs-primary) !important;
}

#pills-tab button.nav-link {
    padding: 5px 20px !important;
    font-size: 15px !important;
    margin: 0px 10px !important;
}
</style>

<style scoped>
.nav-tabs {
    border-bottom: none;
}
.account-nav {
    border: 1px solid var(--bs-primary);
    border-radius: 23px;
    margin: 0px 0 20px 0 !important;
    width: 100%;
}
.account-nav .account-nav-item {
    flex: 0 0 50%;
    width: 50%;
}
.nav-tabs .nav-item.show .nav-link,
.nav-tabs .nav-link {
    align-items: center;
    column-gap: 11px;
    color: var(--bs-primary);
    padding: 9px 17px;
    border: 0;
    border-radius: 23px;
    width: 100%;
    font-size: 20px;
    line-height: 20px;
    letter-spacing: 0.3px;
    margin-bottom: 0;
    margin: 0px 0px !important;
}
.nav-tabs .nav-item.show .nav-link,
.nav-tabs .nav-link.active {
    color: #ffffff !important;
    background-color: var(--bs-primary) !important;
}
#pills-tab button.nav-link {
    padding: 8px 20px !important;
    margin: 0 !important;
}
</style>
