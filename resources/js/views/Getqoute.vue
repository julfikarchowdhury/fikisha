<template>
    <skeleton-loader height="100%" width="100%" :loading="loadingIn">
        <!-- hero banner -->
        <section class="container-fluid p-0">
            <div class="hero">
                <!-- banner item -->
                <div class="hero-banner image" :style="{'background':'url(' + breadcrumb.banner + ')'}" >
                <!--  <div class="hero-banner image"   >-->
                    <div class="container h-100">
                        <div class="row align-items-center  h-100">
                            <div class="col-lg-12">
                                <h4 class=" mt-5 display-5 font-weight-bold text-white text-capitalize">
                                    {{ page.title }}<br />
                                    <span class="text-primary typer"></span>
                                </h4>
                                <p class=" mt-2 text-white">
                                    {{page.description }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end banner item -->
            </div>
        </section>
        <!-- about us -->
        <section class="container-fluid  py-5 mt-3 ">
            <div class="container py-5">
                <div class="row py-2">
                    <div class="col-lg-12">
                        <h4 class="title text-uppercase mb-5 text-primary">{{ $t('get_qoute') }}</h4> 
                        <iframe :src="getqouteurl" width="100%" height="600"></iframe> 
                    </div>
                </div>
            </div>
        </section>
        <!-- end about us -->
    </skeleton-loader>
</template>
<script>
// console.log(localStorage.getItem("form_data"));
import axios from 'axios';
import { Errors } from "../utils/errors.js";
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import TextInput from "@/Components/TextInput.vue";

import NProgress from 'nprogress';
NProgress.configure({ showSpinner: false });

export default {
    components: {
        TextInput
    },
    data() {
        return {
            page: {},
            breadcrumb: {},
            services: [],
            vehicle_types: [],
            vehicles: [],
            fromData: {
                first_name: '',
                last_name: '',
                email: '',
                phone_number: '',
                service_id: '',
                vehicle_type_id: '',
                vehicle_id: '',
                pickup_location: '',
                drop_location: '',
                loading_date: '',
                hours: '',
                minutes: '',
                source: '',
                pickup_lat:'',
                pickup_long:'',
                drop_latitude:'',
                drop_longitude:''
            },
            getqouteurl: window.location.origin + '/get-a-qoute',
            errors: new Errors(),
            isDisabled: false,
            loadingIn: false,
        }
    },
    mounted() {
        this.getServiceData();
        this.bookingPageData();
        this.Locations();
    },
    methods: {
        Locations() {
            
            if (localStorage.getItem("form_data")) {
                const FormD = JSON.parse(localStorage.getItem("form_data"));

                this.fromData.pickup_location = FormD.pickup_location;
                this.fromData.drop_location   = FormD.drop_location;
                
                this.fromData.pickup_lat           = FormD.pickup_lat;
                this.fromData.pickup_long          = FormD.pickup_long;
                this.fromData.drop_latitude        = FormD.drop_latitude;
                this.fromData.drop_longitude       = FormD.drop_longitude;

                this.getqouteurl          = window.location.origin + '/get-a-qoute?pickup_lat=' + this.fromData.pickup_lat + '&pickup_long=' + this.fromData.pickup_long +'&pickup_location=' + this.fromData.pickup_location + '&drop_lat=' + this.fromData.drop_latitude + '&drop_long=' + this.fromData.drop_longitude+'&drop_location=' + this.fromData.drop_location;
            } 
            
           
        },
        bookingPageData() {
            this.loadingIn = true;
            axios.get(config.API_URL_ROOT+'v10/pages/booking',
                {
                    headers: {
                        Accept: "application/json",
                        apiKey: config.API_KEY,
                        ContentType: "application/json",
                    }
                })
                .then((res) => {
                    this.page = res.data.data.page;
                    this.breadcrumb = res.data.data.breadcrumb;
                })
                .finally(() => {
                    this.loadingIn = false;
                });
        },
        getServiceData() {
            this.loadingIn = true;
            axios.get(config.API_URL_ROOT+'v10/get/service',
                {
                    headers: {
                        Accept: "application/json",
                        apiKey: config.API_KEY,
                        ContentType: "application/json",
                    }
                })
                .then((res) => {
                    // this.services = res.data.data.services;
                })
                .finally(() => {
                    this.loadingIn = false;
                });
        },
        getVehicleType() {
            NProgress.start();
            const service_id = this.fromData.service_id;
            axios.get(config.API_URL_ROOT+'v10/get/vehicle_type/' + service_id,
                {
                    headers: {
                        Accept: "application/json",
                        apiKey: config.API_KEY,
                        ContentType: "application/json",
                    }
                })
                .then((res) => {
                    // this.vehicle_types = res.data.data.vehicle_types;
                })
                .finally(() => {
                    NProgress.done();
                });
        },
        getVehicleTypeWiseVehicle() {
            NProgress.start();
            const vehicle_type_id = this.fromData.vehicle_type_id;
            axios.get(config.API_URL_ROOT+'v10/get/vehicle/' + vehicle_type_id,
                {
                    headers: {
                        Accept: "application/json",
                        apiKey: config.API_KEY,
                        ContentType: "application/json",
                    }
                })
                .then((res) => {
                    // this.vehicles = res.data.data.vehicles;
                })
                .finally(() => {
                    NProgress.done();
                });
        },
        bookingDataSubmit() {
            NProgress.start();
            this.isDisabled = true;
            axios.post(config.API_URL_ROOT+'v10/booking/data/submit', this.fromData,{
                    headers: {
                        Accept: "application/json",
                        apiKey: config.API_KEY,
                        ContentType: "application/json",
                    }
                })
                .then((res) => {
                    this.errors = new Errors(),
                        this.fromData = {
                            first_name: '',
                            last_name: '',
                            email: '',
                            phone_number: '',
                            service_id: '',
                            vehicle_type_id: '',
                            vehicle_id: '',
                            pickup_location: '',
                            drop_location: '',
                            loading_date: '',
                            hours: '',
                            minutes: '',
                            source: '',
                        },
                        toast.success(res.data.message, {
                            autoClose: 3000,
                            position: 'bottom-right'
                        });
                })
                .catch((err) => {
                    console.log(err);
                    if (err.response.data.message == "error_validation") {
                        this.errors.record(err.response.data.data.message);
                    } else {
                        toast.error(err.data.message, {
                            autoClose: 3000,
                            position: 'bottom-right'
                        });
                    }
                })
                .finally(() => {
                    this.isDisabled = false;
                    NProgress.done();
                });
        },
    },
}
</script>
 