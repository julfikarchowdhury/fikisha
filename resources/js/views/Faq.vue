<template>
    <skeleton-loader height="100%" width="100%" :loading="loadingIn">
        <!-- hero banner -->
        <section class="container-fluid p-0">
            <div class="hero">
                <!-- banner item -->
                <div class="hero-banner image">
                    <div class="container h-100">
                        <div class="row align-items-center  h-100">
                            <div class="col-lg-12">
                                <h4 class=" mt-5 display-5 font-weight-bold text-white text-capitalize">
                                    {{ page.title }}<br />
                                    <span class="text-primary typer"></span>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end banner item -->
            </div>
        </section>
        <!-- about us -->
        <section class="container-fluid py-5">
            <div class="container faq">
                <div class="row py-2">
                   
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane show active" id="pills-shipper" role="tabpanel"
                            aria-labelledby="pills-home-tab">
                            <h3 class="font-weight-bold mb-3 fs-5 text-uppercase">{{ $t('faq_title') }}</h3>
                            <div class="accordion" id="accordionShipper">
                                <div v-for="(faq_item, i) in faq_list" :key="faq_item.id"
                                    class="accordion-item">
                                    <h2 class="accordion-header" :id="`heading${faq_item.id}`">
                                        <button class="accordion-button fw-bold" :class="{ collapsed: i > 0 }" type="button"
                                            data-bs-toggle="collapse" :data-bs-target="`#collapse${faq_item.id}`"
                                            :aria-expanded="i === 0" :aria-controls="`collapse${faq_item.id}`">
                                            {{ faq_item.question }}
                                        </button>
                                    </h2>
                                    <div :id="`collapse${faq_item.id}`"   class="accordion-collapse collapse" :class="{ show: i === 0}"
                                        :aria-labelledby="`heading${faq_item.id}`" data-bs-parent="#accordionShipper">
                                        <div class="accordion-body">
                                            <Transition>
                                                <p v-html="faq_item.answer"></p>
                                            </Transition>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                         
                    </div>
                </div>
            </div>
        </section>
        <!-- end about us -->
    </skeleton-loader>
</template>
<script>
import axios from 'axios';
export default {
    data() {
        return {
            page: {},
            faq_list: [], 
            loadingIn: false,
        }
    },
    mounted() {
        this.aboutUsData();
    },
    methods: {
        aboutUsData() {
            this.loadingIn = true;
            axios.get(config.API_URL_ROOT+'v10/pages/faq',
                {
                    headers: {
                        Accept: "application/json",
                        apiKey: config.API_KEY,
                        ContentType: "application/json",
                    }
                })
                .then((res) => { 
                    this.page = res.data.data.page;
                    this.faq_list = res.data.data.faq_list; 
                })
                .finally(() => {
                    this.loadingIn = false;
                });
        }
    },
}
</script>
<style>
    .image {
        background-image: url('./../../images/banner.png');
    }

    .v-enter-active,
    .v-leave-active {
        transition: opacity 0.5s ease;
    }

    .v-enter-from,
    .v-leave-to {
        opacity: 0;
    }

    #pills-tab button.nav-link{
        padding:5px 20px!important;
        font-size:15px!important;
        margin:0px 10px!important; 
    }
</style>
