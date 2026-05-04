<template>
    <skeleton-loader height="100%" width="100%" :loading="loadingIn">
        <!-- hero banner -->
        <section class="container-fluid p-0">
            <div class="hero">
                <!-- banner item -->
                <div class="hero-banner image">
                    <div class="container h-100">
                        <div class="row align-items-center h-100">
                            <div class="col-lg-12">
                                <h4
                                    class="mt-5 display-5 font-weight-bold text-white text-capitalize"
                                >
                                    {{ $t("truck_service") }}<br />
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
                <div class="row">
                    <div
                        class="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-4 col-xxl-4 mt-4"
                    >
                        <div class="card">
                            <div class="card-body">
                                <div
                                    class="input-group mb-3"
                                    style="
                                        border: 1px solid #00000021 !important;
                                        border-radius: 5px;
                                    "
                                >
                                    <div
                                        class="input-group-prepend"
                                        style="height: 45px"
                                    >
                                        <span
                                            class="input-group-text bg-white h-100"
                                            style="border: none"
                                        >
                                            <i
                                                class="fa fa-circle-arrow-up text-success"
                                            ></i>
                                        </span>
                                    </div>
                                    <input
                                        type="hidden"
                                        v-model="formData.pickup_lat"
                                        required=""
                                    />
                                    <input
                                        type="hidden"
                                        v-model="formData.pickup_long"
                                        required=""
                                    />
                                    <TextInput
                                        type="hidden"
                                        id="pickup_location"
                                        required=""
                                        v-model="formData.pickup_location_show"
                                    />
                                    <GMapAutocomplete
                                        class="form-control"
                                        style="border: none !important"
                                        :value="formData.pickup_location_show"
                                        :placeholder="$t('pickup_location')"
                                        @place_changed="setPlace"
                                    >
                                    </GMapAutocomplete>
                                </div>
                                <GMapMap
                                    :center="pickupCoords"
                                    :zoom="10"
                                    map-type-id="terrain"
                                    style="display: none"
                                >
                                    <!-- Marker to display the searched location -->
                                    <GMapMarker
                                        :key="pickupMarkerDetails.id"
                                        :position="pickupMarkerDetails.position"
                                        :clickable="true"
                                        :draggable="false"
                                        @click="
                                            pickupOpenMarker(
                                                pickupMarkerDetails.id
                                            )
                                        "
                                    >
                                        <!-- InfoWindow to display the searched location details -->
                                        <GMapInfoWindow
                                            v-if="
                                                pickupLocationDetails.address !=
                                                ''
                                            "
                                            :closeclick="true"
                                            @closeclick="pickupOpenMarker(null)"
                                            :opened="
                                                pickupOpenedMarkerID ===
                                                pickupMarkerDetails.id
                                            "
                                            :options="{
                                                pixelOffset: {
                                                    width: 10,
                                                    height: 0,
                                                },
                                                maxWidth: 320,
                                                maxHeight: 320,
                                            }"
                                        >
                                        </GMapInfoWindow>
                                    </GMapMarker>
                                </GMapMap>
                                <div
                                    class="input-group mb-3"
                                    style="
                                        border: 1px solid #00000021 !important;
                                        border-radius: 5px;
                                    "
                                >
                                    <div
                                        class="input-group-prepend"
                                        style="height: 45px"
                                    >
                                        <span
                                            class="input-group-text bg-white h-100"
                                            style="border: none"
                                        >
                                            <i
                                                class="fa fa-circle-arrow-down text-danger"
                                            ></i>
                                        </span>
                                    </div>
                                    <input
                                        type="hidden"
                                        v-model="formData.drop_latitude"
                                        required=""
                                    />
                                    <input
                                        type="hidden"
                                        v-model="formData.drop_longitude"
                                        required=""
                                    />
                                    <TextInput
                                        type="hidden"
                                        id="drop_location"
                                        required=""
                                        v-model="formData.drop_location"
                                    />
                                    <GMapAutocomplete
                                        class="form-control"
                                        style="border: none !important"
                                        :value="formData.drop_location_show"
                                        :placeholder="$t('drop_location')"
                                        @place_changed="setDropPlace"
                                    >
                                    </GMapAutocomplete>
                                </div>
                                <button-loader
                                    :isLoading="isDisabledNow"
                                    @click="hireTruckNow"
                                    size="btn-lg"
                                    :name="$t('hire_truck')"
                                ></button-loader>
                            </div>
                        </div>
                    </div>
                    <div
                        class="col-12 col-sm-6 col-md-8 col-lg-8 col-xl-8 col-xxl-8 mt-4"
                    >
                        <div class="row">
                            <div
                                class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12"
                            >
                                <h3 class="fw-bold mb-2">
                                    {{ $t("select_your_truck") }}
                                </h3>
                                <ul class="nav nav-tabs account-nav">
                                    <li
                                        class="nav-item account-nav-item"
                                        role="presentation"
                                        v-for="(service, index) in services"
                                        :key="service.id"
                                    >
                                        <button
                                            class="nav-link"
                                            :class="[
                                                index == 0 ? 'active' : '',
                                            ]"
                                            :id="
                                                'service' + service.id + '-tab'
                                            "
                                            data-bs-toggle="pill"
                                            :data-bs-target="
                                                '#service' + service.id
                                            "
                                            type="button"
                                            role="tab"
                                            :aria-controls="
                                                'service' + service.id
                                            "
                                            aria-selected="true"
                                        >
                                            {{ service.name }}
                                        </button>
                                    </li>
                                </ul>
                                <div class="tab-content" id="pills-tabContent">
                                    <div
                                        v-for="(service, index) in services"
                                        :key="service.id"
                                        class="tab-pane"
                                        :class="[
                                            index == 0 ? 'show active' : '',
                                        ]"
                                        :id="'service' + service.id"
                                        role="tabpanel"
                                        :aria-labelledby="
                                            'service' + service.id + '-tab'
                                        "
                                        tabindex="0"
                                    >
                                        <div class="row">
                                            <div
                                                class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-4 col-xxl-4 mb-2"
                                                v-for="vehicle in vehicles"
                                                :key="vehicle.id"
                                                v-show="
                                                    service.id ==
                                                    vehicle.service_id
                                                "
                                            >
                                                <div
                                                    class="service-item card service-item-clr-5 rounded-4 h-100 shadow-sm p-3"
                                                >
                                                    <skeleton
                                                        v-if="loading"
                                                    ></skeleton>
                                                    <img
                                                        v-else
                                                        :src="
                                                            vehicle.vehicle_image
                                                        "
                                                        :alt="
                                                            vehicle.vehicle_name
                                                        "
                                                        class="img-fluid rounded"
                                                        style="height: 160px"
                                                    />
                                                    <div class="card-body py-0">
                                                        <!-- <h4 class="mb-3 font-weight-bold text-dark text-center">{{ vehicle.service_name }}</h4> -->
                                                        <ul
                                                            class="list-unstyled mt-3"
                                                        >
                                                            <li
                                                                class="text-gray-dark"
                                                            >
                                                                <i
                                                                    class="fa fa-circle-right"
                                                                ></i>
                                                                {{
                                                                    $t(
                                                                        "vehicle"
                                                                    )
                                                                }}:
                                                                {{
                                                                    vehicle.vehicle_name
                                                                }}
                                                            </li>
                                                            <li
                                                                class="text-gray-dark"
                                                            >
                                                                <i
                                                                    class="fa fa-circle-right"
                                                                ></i>
                                                                {{
                                                                    $t(
                                                                        "distance"
                                                                    )
                                                                }}:
                                                                {{
                                                                    vehicle.distance_km
                                                                }}
                                                                {{ $t("km") }}
                                                            </li>
                                                            <li
                                                                class="text-gray-dark"
                                                            >
                                                                <i
                                                                    class="fa fa-circle-right"
                                                                ></i>
                                                                {{
                                                                    $t(
                                                                        "charge"
                                                                    )
                                                                }}:
                                                                {{
                                                                    vehicle.amount
                                                                }}/{{
                                                                    vehicle.distance_type
                                                                }}
                                                            </li>
                                                            <li
                                                                class="text-gray-dark"
                                                            >
                                                                <i
                                                                    class="fa fa-circle-right"
                                                                ></i>
                                                                {{
                                                                    $t(
                                                                        "capacity"
                                                                    )
                                                                }}:
                                                                {{
                                                                    vehicle.capacity
                                                                }}
                                                            </li>
                                                            <li
                                                                class="text-gray-dark"
                                                            >
                                                                <i
                                                                    class="fa fa-circle-right"
                                                                ></i>
                                                                {{
                                                                    $t("type")
                                                                }}:
                                                                {{
                                                                    vehicle.vehicle_type
                                                                }}
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div
                                                        class="card-footer text-center"
                                                        style="background: none"
                                                    >
                                                        <button-multi
                                                            :isLoading="
                                                                isDisabled
                                                            "
                                                            size="btn-lg"
                                                            :currentIndex="
                                                                currentIndex
                                                            "
                                                            :index="
                                                                vehicle.vehicle_id
                                                            "
                                                            :name="
                                                                $t('order_now')
                                                            "
                                                            @click="
                                                                orderNow(
                                                                    vehicle
                                                                )
                                                            "
                                                        ></button-multi>
                                                    </div>
                                                </div>
                                            </div>
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
import axios from "axios";
import { onMounted, ref } from "vue";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import ButtonMulti from "@/Components/ButtonLoaderMulti.vue";
import ButtonLoader from "@/Components/ButtonLoader.vue";
import TextInput from "@/Components/TextInput.vue";

import NProgress from "nprogress";
NProgress.configure({ showSpinner: false });

export default {
    setup() {
        const loading = ref(false);
        const loadingIn = ref(false);
        const isDisabled = ref(false);
        const isDisabledNow = ref(false);
        const currentIndex = ref("");
        const services = ref([]);
        const vehicles = ref([]);
        const formData = ref({
            pickup_lat: "",
            pickup_long: "",
            pickup_location: "",
            pickup_location_show: "",
            drop_location: "",
            drop_latitude: "",
            drop_longitude: "",
            drop_location_show: "",
        });

        // Pickup location map
        // Setting the default coordinates to London
        const pickupCoords = ref({ lat: 51.5072, lng: 0.1276 });
        // Marker Details
        const pickupMarkerDetails = ref({
            id: 1,
            position: pickupCoords.value,
        });

        // Places Details
        const pickupLocationDetails = ref({
            address: "",
            url: "",
        });

        // Set the location based on the place selected
        // Set the location based on the place selected
        const setPlace = (place) => {
            pickupCoords.value.lat = place.geometry.location.lat();
            pickupCoords.value.lng = place.geometry.location.lng();
            // form data
            formData.value.pickup_lat = place.geometry.location.lat();
            formData.value.pickup_long = place.geometry.location.lng();
            formData.value.pickup_location = place.formatted_address;
            formData.value.pickup_location_show = place.formatted_address;

            // Update the location details
            pickupLocationDetails.value.address = place.formatted_address;
            pickupLocationDetails.value.url = place.url;
        };

        const setDropPlace = (place) => {
            // form Data
            formData.value.drop_latitude = place.geometry.location.lat();
            formData.value.drop_longitude = place.geometry.location.lng();
            formData.value.drop_location = place.formatted_address;
            formData.value.drop_location_show = place.formatted_address;
        };

        const getLocationService = (fromDataNew) => {
            loadingIn.value = true;
            loading.value = true;
            axios
                .post(
                    config.API_URL_ROOT + "v10/find/vehicle/calculation/web",
                    fromDataNew,
                    {
                        headers: {
                            Accept: "application/json",
                            apiKey: config.API_KEY,
                            ContentType: "application/json",
                        },
                    }
                )
                .then((res) => {
                    vehicles.value = res.data.data.vehicle_data;
                    services.value = res.data.data.services;
                })
                .finally(() => {
                    loadingIn.value = false;
                    setTimeout(() => {
                        loading.value = false;
                    }, 800);
                });
        };

        const hireTruckNow = () => {
            localStorage.setItem("form_data", JSON.stringify(formData.value));
            isDisabledNow.value = true;
            loading.value = true;
            axios
                .post(
                    config.API_URL_ROOT + "v10/find/vehicle/calculation/web",
                    formData.value,
                    {
                        headers: {
                            Accept: "application/json",
                            apiKey: config.API_KEY,
                            ContentType: "application/json",
                        },
                    }
                )
                .then((res) => {
                    vehicles.value = res.data.data.vehicle_data;
                })
                .finally(() => {
                    isDisabledNow.value = false;
                    loading.value = false;
                });
        };

        const orderNow = (vehicle) => {
            currentIndex.value = vehicle.vehicle_id;
            isDisabled.value = true;
            NProgress.start();
            axios
                .get(config.API_URL_ROOT + "v10/order/auth/check", {
                    headers: {
                        Accept: "application/json",
                        apiKey: config.API_KEY,
                        ContentType: "application/json",
                    },
                })
                .then((res) => {
                    const orderData = formData.value;
                    if (res.data.data.auth == true) {
                        if (vehicle.distance_type == "Per Hour") {
                            location.href =
                                "/shipper-order/create?service_id=" +
                                vehicle.service_id +
                                "&vehicle_id=" +
                                vehicle.vehicle_id +
                                "&hours=1&pickup_lat=" +
                                orderData.pickup_lat +
                                "&pickup_long=" +
                                orderData.pickup_long +
                                "&pickup_location=" +
                                orderData.pickup_location +
                                "&drop_latitude=" +
                                orderData.drop_latitude +
                                "&drop_longitude=" +
                                orderData.drop_longitude +
                                "&drop_location=" +
                                orderData.drop_location +
                                "&distance_km=" +
                                vehicle.distance_km;
                        } else {
                            location.href =
                                "/shipper-order/create?service_id=" +
                                vehicle.service_id +
                                "&vehicle_id=" +
                                vehicle.vehicle_id +
                                "&pickup_lat=" +
                                orderData.pickup_lat +
                                "&pickup_long=" +
                                orderData.pickup_long +
                                "&pickup_location=" +
                                orderData.pickup_location +
                                "&drop_latitude=" +
                                orderData.drop_latitude +
                                "&drop_longitude=" +
                                orderData.drop_longitude +
                                "&drop_location=" +
                                orderData.drop_location +
                                "&distance_km=" +
                                vehicle.distance_km;
                        }
                    }
                })
                .catch((err) => {
                    toast.error(err.response.data.data.msg, {
                        autoClose: 3000,
                        position: "bottom-right",
                    });
                    if (err.response.data.data.auth == false) {
                        location.href = "/login";
                    }
                })
                .finally(() => {
                    NProgress.done();
                    isDisabled.value = false;
                });
        };

        onMounted(async () => {
            const fromDataNew = JSON.parse(localStorage.getItem("form_data"));
            formData.value.pickup_lat = fromDataNew.pickup_lat;
            formData.value.pickup_long = fromDataNew.pickup_long;
            formData.value.pickup_location = fromDataNew.pickup_location;
            formData.value.pickup_location_show =
                fromDataNew.pickup_location_show;
            formData.value.drop_location = fromDataNew.drop_location;
            formData.value.drop_latitude = fromDataNew.drop_latitude;
            formData.value.drop_longitude = fromDataNew.drop_longitude;
            formData.value.drop_location_show = fromDataNew.drop_location;
            getLocationService(fromDataNew);
            if (!fromDataNew) {
                toast.error("Place again search", {
                    autoClose: 3000,
                    position: "bottom-right",
                });
                location.href = "/";
            }
        });

        return {
            loading,
            isDisabled,
            isDisabledNow,
            currentIndex,
            loadingIn,
            setPlace,
            setDropPlace,
            // pickup map
            pickupCoords,
            pickupMarkerDetails,
            pickupLocationDetails,
            getLocationService,
            orderNow,
            hireTruckNow,
            services,
            vehicles,
            formData,
        };
    },
    components: {
        ButtonMulti,
        ButtonLoader,
        TextInput,
    },
};
</script>
<style scoped>
.image {
    background-image: url("./../../images/banner.png");
}
.account-nav {
    margin: 0px 0 20px 0 !important;
}

.nav-tabs .nav-item.show .nav-link,
.nav-tabs .nav-link {
    color: #333 !important;
    border: 0;
    font-size: 16px;
}

.nav-tabs .nav-item.show .nav-link,
.nav-tabs .nav-link.active {
    color: var(--bs-primary) !important;
    background-color: transparent !important;
    border-bottom: 1px solid red;
}

#pills-tab button.nav-link {
    padding: 8px 20px !important;
    margin: 0 !important;
}
</style>
