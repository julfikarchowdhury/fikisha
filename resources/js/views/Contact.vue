<template>
    <skeleton-loader height="100%" width="100%" :loading="loadingIn">
        <!-- hero banner -->
        <section class="container-fluid p-0">
            <div class="hero">
                <!-- banner item -->
                     <div class="hero-banner image" :style="{'background':'url(' + breadcrumb.banner + ')'}" >
                    <div class="container h-100">
                        <div class="row align-items-center h-100">
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
        <section class="container-fluid py-5 mt-3">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <h4 class="title text-uppercase mb-2 text-primary">{{ page.title }}</h4>
                        <form @submit.prevent="contactUsDataSubmit()">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group mt-3">
                                        <input type="text" v-model="fromData.name" class="form-control" :placeholder="$t('enter_your_name')" />
                                        <span v-if="errors.has('name')" class="text-danger" >
                                            {{ errors.get("name") }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group mt-3">
                                        <input type="email" v-model="fromData.email" class="form-control" :placeholder="$t('enter_your_email')" />
                                        <span v-if="errors.has('email')" class="text-danger" >
                                            {{ errors.get("email") }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group mt-3">
                                        <input type="text" v-model="fromData.subject" class="form-control" :placeholder="$t('enter_your_subject')" />
                                        <span v-if="errors.has('subject')" class="text-danger" >
                                            {{ errors.get("subject") }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group mt-3">
                                        <textarea v-model="fromData.message" class="form-control" rows="5" :placeholder="$t('enter_your_message')" ></textarea>
                                        <span v-if="errors.has('message')" class="text-danger" >
                                            {{ errors.get("message") }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <button-loader :isLoading="isDisabled" size="btn-lg" :name="$t('submit')"></button-loader>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-6 mt-5">
                        <div class="row mt-5 mt-lg-0">
                            <div v-html="page.description"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="bg-gray-white py-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-6 mb-3">
                        <div class="card guaranties-card shadow-sm p-3 h-100">
                            <div class="card-body">
                                <div class="text-center align-items-center mt-4">
                                   <i class="fa fa-location-dot me-2 fs-2"></i> 
                                    <div class="text-center mt-2">
                                        <div class="text-dark-blue font-weight-bold"  >
                                        {{ $t('address') }}
                                        </div>
                                    </div>
                                    <div class="mt-2" >{{ contact_us.address }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6 mb-3">
                        <div class="card guaranties-card shadow-sm p-3 h-100">
                            <div class="card-body">
                                <div class="text-center align-items-center mt-4">
                                    <i class="fa fa-phone me-2 fs-2"></i> 
                                    <div class="text-center mt-2">
                                        <div class="text-dark-blue font-weight-bold"  >
                                        {{ $t('phone') }}
                                        </div>
                                    </div>
                                    <div class="mt-2" >{{ contact_us.phone }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6 mb-3">
                        <div class="card guaranties-card shadow-sm p-3 h-100">
                            <div class="card-body">
                                <div class="text-center align-items-center mt-4">
                                <i class="fa fa-envelope me-2 fs-2"></i> 
                                    <div class="text-center mt-2">
                                        <div class="text-dark-blue font-weight-bold"  >
                                            {{ $t('email_address') }}
                                        </div>
                                    </div>
                                    <div class="mt-2" >{{ contact_us.email }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6 mb-3">
                        <div class="card guaranties-card shadow-sm p-3 h-100">
                            <div class="card-body">
                                <div class="text-center align-items-center mt-4">
                                    <i class="fa fa-globe me-2 fs-2"></i> 
                                    <div class="text-center mt-2">
                                        <div class="text-dark-blue font-weight-bold"  >
                                            {{ $t('website') }}
                                        </div>
                                    </div>
                                    <div class="mt-2" >{{ contact_us.website }}</div>
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
import { Errors } from "../utils/errors.js";
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import ButtonLoader from "@/Components/ButtonLoader.vue";

import NProgress from 'nprogress';
NProgress.configure({ showSpinner: false });

export default {
    components: {
        ButtonLoader
    },
    data() {
        return {
            contact_us: {},
            breadcrumb: {},
            page: {},
            fromData: {
                name: '',
                email: '',
                subject: '',
                message: '',
            },
            errors: new Errors(),
            isDisabled: false,
            loadingIn: false,
        }
    },
    mounted() {
        this.contactUsData();
    },
    methods: {
        contactUsDataSubmit() {
            NProgress.start();
            this.isDisabled = true;
            axios.post(config.API_URL_ROOT+'v10/pages/contact/message/send', this.fromData,{
                    headers: {
                        Accept: "application/json",
                        apiKey: config.API_KEY,
                        ContentType: "application/json",
                    }
                })
                .then((res) => {
                    console.log(res);
                    toast.success(res.data.message, {
                        autoClose: 3000,
                        position: 'bottom-right'
                    });
                })
                .catch((err) => {
                    if(error.errors){

                        toast.error(err.message, {
                            autoClose: 3000,
                            position: 'bottom-right'
                        });
                    }
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
        contactUsData() {
            this.loadingIn = true;
            axios.get(config.API_URL_ROOT+'v10/pages/contact', {
                headers: {
                    Accept: "application/json",
                    apiKey: config.API_KEY,
                    ContentType: "application/json",
                }
            })
                .then((res) => {
                    this.page       = res.data.data.page;
                    this.contact_us = res.data.data.contact;
                    this.breadcrumb = res.data.data.breadcrumb;
                })
                .finally(() => {
                    this.isDisabled = false;
                    this.loadingIn = false;
                });
        }
    },
}
</script>
 