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
                                    {{ $t('order_logs') }}<br />
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
            <div class="container mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="row justify-content-center">
                            <div class="col-sm-6">
                                <div class="input-group mb-3" style="border: 1px solid #00000021 !important;border-radius:5px">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white h-100" style="border:none">
                                            <i class="fa fa-history text-danger"></i>
                                        </span>
                                    </div>
                                    <TextInput type="text" id="tracking_id" class="form-control"
                                        style="border:none!important;" required=""
                                        v-model="trackingFormData.tracking_id" :placeholder="$t('tracking_id')" />
                                    <button type="button" @click="trackingNow()" :disabled="isDisabled"
                                        class="btn btn-lg btn-primary text-white"
                                        style="padding:.3rem 1rem !important; color: white !important;border:none!important;">
                                        {{ $t('track_now') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="card">
                    <div class="card-body">
                        <h4 class="font-weight-bold fs-5 text-center" v-if="currentTrackingId">
                            {{ $t('order_tracking_id') }}: # {{ currentTrackingId }}
                        </h4>
                    </div>
                    <skeleton v-if="loading"></skeleton>
                    <div v-else class="timeline">
                        <div class="row" v-if="order_tracking_history.length > 0">
                            <div class="col-md-12">
                                <div class="parcel-oprations">
                                    <section class="cd-timeline js-cd-timeline">
                                        <div class="cd-timeline__container">
                                            <div class="cd-timeline__block js-cd-block"
                                                v-for="history in order_tracking_history" :key="history.id">
                                                <div class="cd-timeline__img cd-timeline__img--picture js-cd-img">
                                                    <i class="timeline_icon fas fa-check" aria-hidden="true"></i>
                                                </div>
                                                <!-- cd-timeline__img -->
                                                <div class="cd-timeline__content js-cd-content">
                                                    <strong>{{ history.status_name }}</strong><br>
                                                    <span  v-if="history.delivery_man">Driver Name: {{ history.delivery_man?.user?.name??'' }}</span><br v-if="history.delivery_man"> 
                                                    <span  v-if="history.delivery_man">Driver Phone: {{ history.delivery_man?.user?.mobile??'' }}</span><br v-if="history.delivery_man">
                                                
                                                    <span>Note: {{ history.note }}</span><br>
                                                    <strong>Created By</strong><br>
                                                    <span>Name: {{ history.user.name }}</span><br>
                                                    <span>Mobile: {{ history.user.mobile }}</span><br>
                                                    <div class="cd-timeline__date">
                                                        <strong>{{ new Date(history.created_at).toDateString()  }}</strong><br>
                                                        <small>{{  new Date(history.created_at).toLocaleTimeString()  }}</small>
                                                    </div>
                                                </div>
                                                <!-- cd-timeline__content -->
                                            </div>
                                        </div>
                                    </section>
                                    <!-- cd-timeline -->
                                </div>
                            </div>
                        </div>
                        <div v-else class="row my-5">
                            <div class="col-lg-6 m-auto">
                                <img src="./../../images/parcel-was-not-found.png" width="100%"/>
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
import TextInput from "@/Components/TextInput.vue";
import ButtonLoader from "@/Components/ButtonLoader.vue";
import { onMounted, ref } from 'vue';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

import NProgress from 'nprogress';
import { DatetimeFormat } from 'vue-i18n';
NProgress.configure({ showSpinner: false });

export default {
    setup() {
        const loading = ref(false);
        const isDisabled = ref(false);
        const loadingIn = ref(false);
        const order_tracking_history = ref([]);
        const trackingFormData = ref({
            tracking_id: '',
        });
        const currentTrackingId = ref(localStorage.getItem("tracking_id") || "");

        const getOrderTrackingHistory = (id) => {
            if (isTrackingToken(id)) {
                location.href = "/track/" + id;
                return;
            }
            trackingFormData.value.tracking_id = id;
            isDisabled.value = true;
            loadingIn.value = true;
            loading.value = true;
            axios.get(config.API_URL_ROOT+'v10/parcel/tracking/' + id, {
                headers: {
                    Accept: "application/json",
                    apiKey: config.API_KEY,
                    ContentType: "application/json",
                }
            }).then((res) => {
                
                order_tracking_history.value = res.data.data.events;
            }).finally(() => {
                loadingIn.value = false;
                isDisabled.value = false;
                setTimeout(() => {
                    loading.value = false;
                }, 800);
            });
        }

        const getOrderTrackingHistoryFilter = (new_tracking_id) => {
            if (isTrackingToken(new_tracking_id)) {
                location.href = "/track/" + new_tracking_id;
                return;
            }
            isDisabled.value = true;
            loading.value    = true;
 
            axios.get(config.API_URL_ROOT+'v10/parcel/tracking/' + new_tracking_id, {
                headers: {
                    Accept: "application/json",
                    apiKey: config.API_KEY,
                    ContentType: "application/json",
                }
            }).then((res) => { 
                order_tracking_history.value = res.data.data.events; 
          
            }).finally(() => {
                isDisabled.value = false;
                setTimeout(() => {
                    loading.value = false;
                });
            });
        }

        const trackingNow = () => {
          
            const newTrackingId = (trackingFormData.value.tracking_id || '').trim();
            if (!newTrackingId) {
                toast.error("Enter tracking code", {
                    autoClose: 2500,
                    position: 'bottom-right'
                });
                return;
            }

            currentTrackingId.value = newTrackingId;
            localStorage.setItem("tracking_id", newTrackingId);
            getOrderTrackingHistoryFilter(newTrackingId);
        }

        const isTrackingToken = (value) => {
            const token = (value || '').trim();
            return /^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i.test(token);
        }

        onMounted(() => {
            if (!currentTrackingId.value) {
                toast.error("Place again search", {
                    autoClose: 3000,
                    position: 'bottom-right'
                });
                location.href = "/";
                return;
            }
            getOrderTrackingHistory(currentTrackingId.value);
        });

        return {
            isDisabled,
            loading,
            loadingIn,
            getOrderTrackingHistory,
            order_tracking_history,
            currentTrackingId,
            trackingFormData,
            trackingNow,
            getOrderTrackingHistoryFilter,
        }
    },
    components: {
        TextInput,
        ButtonLoader,
    },
}
</script>
<style>
.image {
    background-image: url('./../../images/banner.png');
}
</style>
