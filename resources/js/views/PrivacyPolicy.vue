<template>
    <skeleton-loader height="100%" width="100%" :loading="loadingIn">
        <!-- hero banner -->
        <section class="container-fluid p-0">
            <div class="hero">
                <!-- banner item -->
                <div class="hero-banner image" :style="{'background':'url(' + breadcrumb.banner + ')'}">
                    <div class="container h-100">
                        <div class="row align-items-center h-100">
                            <div class="col-lg-12">
                                <h4 class=" mt-5 display-5 font-weight-bold text-white text-capitalize">
                                       {{ page.title }}<br />
                                    <span class="text-primary typer"></span>
                                </h4>
                                <p class="mt-2 text-white fs-3">
                                    {{ privacy_policy.breadcrumbs_description }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end banner item -->
            </div>
        </section>

        <!-- about us -->
        <section class="container-fluid pt-2">
            <div class="container py-5">
                <div class="row py-2 align-items-center">
                    <div class="col-lg-12">
                        <h4 class="title text-uppercase mb-3 text-primary">{{ page.title }}</h4>
                        <p class="text-left" v-html="page.description"></p>
                    </div>
                </div>
            </div>
        </section>
        <!-- end about us -->
    </skeleton-loader>
</template>
<script>
import axios from 'axios';
import { onMounted, ref } from 'vue';
export default {
    setup() {
        const loadingIn = ref(false);
        const privacy_policy = ref({});
        const page = ref({});
        const breadcrumb = ref({});

        const getData = () => {
            loadingIn.value = true;
            axios.get(config.API_URL_ROOT+'v10/pages/privacy-policy', {
                headers: {
                    Accept: "application/json",
                    apiKey: config.API_KEY,
                    ContentType: "application/json",
                }
            }).then((res) => {
                console.log(res);
                breadcrumb.value = res.data.data.breadcrumb;
                page.value = res.data.data.page;
            }).finally(() => {
                loadingIn.value = false;
            });
        }

        onMounted(() => {
            getData();
        });

        return {
            loadingIn,
            getData,
            privacy_policy,
            page,
            breadcrumb,
        }
    }
}
</script>

 