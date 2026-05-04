<template>
    <skeleton-loader height="100%" width="100%" :loading="loadingIn">
        <!-- hero -->
        <section class="container-fluid p-0">
            <div class="hero">
                <div class="hero-slider">
                    <!-- slider item -->
                    <Carousel v-bind="settings" :breakpoints="breakpoints" fade :transition="500" :autoplay="5000"
                        :wrap-around="true" :wrapAround="true" :pauseAutoplayOnHover="true">
                        <Slide v-for="slider in sliders" :key="slider.id" class="hero-slider-item"
                            :style="{ backgroundImage: 'url(' + slider.slider_image + ')' }">
                            <div class="container h-100vh">
                                <div class="row align-items-center hero-slider-item-row-2 h-100">
                                    <div class="col-lg-7">
                                        <h1 class="display-1 text-white text-uppercase">{{ slider.title }}<br />
                                            <span class="text-primary typer"></span>
                                        </h1>
                                        <p class="fs-3 mt-4 text-white">{{ slider.small_title }}</p>
                                        <div class="slider-btn-box">
                                            <a class="btn btn-lg px-3 m-3 slider-btn animate-button" target="_blank" href="/register">
                                                {{ $t('carriers') }}
                                            </a>
                                            <a class="btn  btn-lg  px-3 m-3 slider-btn animate-button" target="_blank" href="/register">
                                                {{ $t('shippers') }}
                                            </a>
                                            <a class="btn  btn-lg  px-3 m-3 slider-btn animate-button" target="_blank" href="/register">
                                                {{ $t('drive_with_us') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </Slide>
                        <template #addons>
                            <Pagination />
                            <Navigation />
                        </template>
                    </Carousel>
                    <!-- end slider item -->
                </div>
            </div>
        </section>
    </skeleton-loader>
</template>
<script>
import { defineComponent } from 'vue';
import { Carousel, Navigation, Slide, Pagination } from 'vue3-carousel';
import 'vue3-carousel/dist/carousel.css';
import axios from 'axios';
export default defineComponent({
    components: {
        Pagination,
        Carousel,
        Slide,
        Navigation,
    },
    data: () => ({
        // carousel settings
        settings: {
            itemsToShow: 1,
            snapAlign: 'center',
        },
        // breakpoints are mobile first
        // any settings not specified will fallback to the carousel settings
        breakpoints: {
            // 700px and up
            700: {
                itemsToShow: 1,
                snapAlign: 'center',
            },
            // 1024 and up
            1024: {
                itemsToShow: 1,
                snapAlign: 'start',
            },
        }, 
        sliders: [],
        SITE_TITLE: config.SITE_TITLE,
        SITE_TITLE_URL: config.SITE_TITLE_URL,
        loadingIn: false
    }),
    mounted() {
        this.loadingIn = true;
        axios.get(config.API_URL_ROOT+'v10/get/sliders',
            {
                headers: {
                    Accept: "application/json",
                    apiKey: config.API_KEY,
                    ContentType: "application/json",
                }
            }).then(({ data }) => {
                this.sliders = data.data.sliders;
            })
            .finally(() => {
                this.loadingIn = false;
            });
    }
})
</script>
<style>
.carousel__slide {
    padding: 5px;
}

.carousel__viewport {
    perspective: 2000px;
}

.carousel__track {
    transform-style: preserve-3d;
}

.carousel__slide--sliding {
    transition: 0.5s;
}

.carousel__slide {
    opacity: 0.9;
    transform: rotateY(-20deg) scale(0.9);
}

.carousel__slide--active~.carousel__slide {
    transform: rotateY(20deg) scale(0.9);
}

.carousel__slide--prev {
    opacity: 1;
    transform: rotateY(-10deg) scale(0.95);
}

.carousel__slide--next {
    opacity: 1;
    transform: rotateY(10deg) scale(0.95);
}

.carousel__slide--active {
    opacity: 1;
    transform: rotateY(0) scale(1.1);
}
</style>
