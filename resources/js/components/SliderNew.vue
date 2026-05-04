<template>
    <skeleton-loader height="100%" width="100%" :loading="loadingIn">
        <!-- hero -->
        <section class="banner__slider">
            <div class="hero__slider">
                <Carousel
                    :navigation="true"
                    :pagination="false"
                    :startAutoPlay="false"
                    :timeout="5000"
                    class="carousel"
                    v-slot="{ currentSlide }"
                    :slideCount="sliders.length"
                >
                    <template
                        v-for="(slider, index) in sliders"
                        :key="slider.id"
                    >
                        <Slide>
                            <div
                                v-if="currentSlide == index + 1"
                                class="slide__item"
                                :style="{
                                    backgroundImage:
                                        'url(' + slider.slider_image + ')',
                                }"
                            >
                                <!-- <img :src="slider.slider_image" alt="" /> -->
                                <div class="container">
                                    <div class="row align-items-center">
                                        <div class="slide__content text-start">
                                            <h2 class="banner__title">
                                                {{ slider.title }}
                                                <span
                                                    class="text-primary typewriter"
                                                ></span>
                                            </h2>
                                            <p class="banner__description">
                                                {{ slider.small_title }}
                                            </p>
                                            <div
                                                class="banner__buttons d-flex align-items-center gap-3"
                                            >
                                                <a
                                                    href="/register"
                                                    class="btn btn-primary"
                                                    >{{ $t("register") }}</a
                                                >
                                                <a
                                                    href="/login"
                                                    class="btn btn-secondary"
                                                    >{{ $t("login") }}</a
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </Slide>
                    </template>
                </Carousel>
            </div>
        </section>

        <!-- <section class="container-fluid p-0">
            <div class="hero">
                <div class="hero-slider">
                    <Carousel
                        :navigation="true"
                        :pagination="false"
                        :startAutoPlay="false"
                        :timeout="5000"
                        class="carousel"
                        v-slot="{ currentSlide }"
                        :slideCount="sliders.length"
                    >
                        <template
                            v-for="(slider, index) in sliders"
                            :key="slider.id"
                        >
                            <Slide>
                                <div
                                    v-if="currentSlide == index + 1"
                                    class="slide-info hero-slider-item"
                                >
                                    <img :src="slider.slider_image" alt="" />
                                    <div class="container h-100vh">
                                        <div
                                            class="row align-items-center hero-slider-item-row-2 h-80"
                                        >
                                            <div class="col-lg-7 text-start">
                                                <h1
                                                    class="banner-title position-sticky text-white"
                                                >
                                                    {{ slider.title }}<br />
                                                    <span
                                                        class="text-primary typewriter"
                                                    ></span>
                                                </h1>
                                                <p
                                                    class="fs-4 mt-4 text-white position-sticky"
                                                >
                                                    {{ slider.small_title }}
                                                </p>
                                                <div
                                                    class="slider-btn-box mt-5 d-flex align-items-center gap-3"
                                                >
                                                    <a
                                                        class="btn btn-lg px-3 slider-btn animate-button"
                                                        target="_blank"
                                                        href="/register"
                                                    >
                                                        {{ $t("sender") }}
                                                    </a>
                                                    <a
                                                        class="btn btn-lg px-3 slider-btn animate-button"
                                                        target="_blank"
                                                        href="/register"
                                                    >
                                                        {{ $t("driver") }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </Slide>
                        </template>
                    </Carousel>
                </div>
            </div>
        </section> -->
    </skeleton-loader>
</template>
<script>
import Carousel from "../components/Carousel.vue";
import Slide from "../components/Slide.vue";
import axios from "axios";

export default {
    components: {
        Carousel,
        Slide,
    },
    data: () => ({
        sliders: [],
        SITE_TITLE: config.SITE_TITLE,
        SITE_TITLE_URL: config.SITE_TITLE_URL,
        loadingIn: false,
    }),
    created() {
        this.loadingIn = true;
        axios
            .get(config.API_URL_ROOT + "v10/get/sliders", {
                headers: {
                    Accept: "application/json",
                    apiKey: config.API_KEY,
                    ContentType: "application/json",
                },
            })
            .then((res) => {
                this.sliders = res.data.data.sliders;
            })
            .finally(() => {
                this.loadingIn = false;
            });
    },
};
</script>
<style lang="scss" scoped>
.hero-slider-item {
    .banner-title {
        font-size: 62px;
        font-weight: 600;
        line-height: 1.2;
        color: #fff !important;
        margin-bottom: 20px;
    }
    .banner-description {
        font-size: 24px;
        color: #fff !important;
        line-height: 1.5;
        margin-bottom: 0px;
    }

    .banner-buttons {
        margin-top: 20px;

        .btn {
            min-width: 180px;
        }
    }
}
.slide__item {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    padding: 180px 0 250px;
    background-position: center;
    background-size: cover;
    background-repeat: no-repeat;

    .slide__content {
        max-width: 800px;
        z-index: 9;
    }

    .banner__title {
        font-size: 62px;
        font-weight: 600;
        line-height: 1.2;
        color: #fff !important;
        margin-bottom: 20px;

        @media (max-width: 992px) {
            font-size: 52px;
        }

        @media (max-width: 768px) {
            font-size: 42px;
        }
    }
    .banner__description {
        font-size: 24px;
        color: #fff !important;
        line-height: 1.5;
        margin-bottom: 0px;

        @media (max-width: 768px) {
            font-size: 18px;
        }
    }

    .banner__buttons {
        margin-top: 20px;

        .btn {
            min-width: 180px;

            @media (max-width: 576px) {
                min-width: 140px;
            }
        }
    }
}

.carousel {
    min-height: 660px;
}
// .carousel {
//     position: relative;
//     min-height: 600px;
//     // max-height: 80vh;
//     // height: 80vh;

//     .slide-info {
//         position: absolute;
//         top: 0;
//         left: 0;
//         width: 100%;
//         max-height: 100%;
//         height: 100%;
//         z-index: 1;
//         padding: 20px 0 150px;

//         img {
//             position: absolute;
//             left: 0;
//             top: 0;
//             width: 100%;
//             height: 100%;
//             object-fit: cover;
//         }
//     }
// }

// // X-Small devices (portrait phones, less than 576px)
// // No media query for `xs` since this is the default in Bootstrap

// // Small devices (landscape phones, 576px and up)
// @media (min-width: 576px) {
//     .carousel {
//         position: relative;
//         //max-height: 65vh;
//         //height: 65vh;

//         .slide-info {
//             position: absolute;
//             top: 0;
//             left: 0;
//             width: 100%;
//             max-height: 100%;
//             height: 100%;
//             z-index: 1;

//             img {
//                 position: absolute;
//                 left: 0;
//                 top: 0;
//                 width: 100%;
//                 height: 100%;
//                 object-fit: cover;
//             }
//         }
//     }
//     .hero-slider-item {
//         min-height: 65vh !important;
//     }
//     .h-80 {
//         height: 65% !important;
//     }
// }

// // Medium devices (tablets, 768px and up)
// @media (min-width: 768px) {
// }

// // Large devices (desktops, 992px and up)
// @media (min-width: 992px) {
// }

// // X-Large devices (large desktops, 1200px and up)
// @media (min-width: 1200px) {
// }

// // XX-Large devices (larger desktops, 1400px and up)
// @media (min-width: 1400px) {
// }
</style>
