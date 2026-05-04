<template>
    <skeleton-loader height="100%" width="100%" :loading="loadingIn">
    
       <div class="mt-4 brand-slider text-center">
     

                <!-- slider item -->
                <Carousel v-bind="settings" :breakpoints="breakpoints" fade :transition="500" :autoplay="2000"
                    :wrap-around="true" :wrapAround="true" :pauseAutoplayOnHover="true">
                      <Slide v-for="blog in blog_list" :key="blog.id"  > 
                           <img  :src="blog.image"  />
                      </Slide> 
                    <template #addons>
                        <!--<Pagination />-->
                        <Navigation />
                    </template>
                </Carousel>
                <!-- end slider item -->
           </div>
    </skeleton-loader>
</template>
<script>
import { defineComponent } from 'vue';
import { Carousel, Navigation, Slide, Pagination } from 'vue3-carousel';
import 'vue3-carousel/dist/carousel.css';
import axios from 'axios';
import { onMounted, ref } from 'vue';
export default defineComponent({
    setup(){
       
        const blog_list = ref({});
        const loadingIn = ref(false); 

         const blogList = () => { 
            loadingIn.value = true;
            axios.get(config.API_URL_ROOT+'v10/partner',{ 
                    headers: {
                        Accept: "application/json",
                        apiKey: config.API_KEY, 
                        ContentType: "application/json",
                    }
                })
                .then((res) => { 
                   
                    blog_list.value = res.data.data.pertner; 
                })
                .finally(() => {
                    loadingIn.value = false;
                });
        }

        onMounted(() => { 
            blogList();
        })

        return { 
            blogList,
            blog_list
        }

    },
    components: {
        Pagination,
        Carousel,
        Slide,
        Navigation,
    },
    data: () => ({
        // carousel settings
        settings: {
            itemsToShow: 3,
            snapAlign: 'center',
        },
        // breakpoints are mobile first
        // any settings not specified will fallback to the carousel settings
        breakpoints: {
            // 700px and up
            700: {
                itemsToShow: 3,
                snapAlign: 'center',
            },
            // 1024 and up
            1024: {
                itemsToShow: 5,
                snapAlign: 'center',
            },
        }, 
        sliders: [],
        SITE_TITLE: config.SITE_TITLE,
        SITE_TITLE_URL: config.SITE_TITLE_URL,
        loadingIn: false
    }),
    
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
.carousel__prev{
    left:-40px!important;
}
.carousel__next{
    right:-40px!important;
}
</style>
