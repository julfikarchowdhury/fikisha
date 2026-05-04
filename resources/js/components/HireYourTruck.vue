<template>
    <section class="hire-your-track bg-gray-white">
        <div class="container">
            <div class="row">
                <div
                    class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12"
                >
                    <div class="hire-box">
                        <div class="custom__tabs">
                            <div class="text-start">
                                <ul
                                    class="nav nav-pills"
                                    id="pills-tabs"
                                    role="tablist"
                                >
                                    <li
                                        class="nav-item account-nav-item"
                                        role="presentation"
                                    >
                                        <button
                                            class="nav-link active"
                                            id="tracking-tab"
                                            data-bs-toggle="pill"
                                            data-bs-target="#tracking"
                                            type="button"
                                            role="tab"
                                            aria-controls="tracking"
                                            aria-selected="true"
                                        >
                                            {{ $t("tracking") }}
                                        </button>
                                    </li>
                                </ul>
                            </div>

                            <!-- Tab content -->
                            <div class="tab-content mt-3" id="pills-tabContent">
                                <div
                                    class="tab-pane show active"
                                    id="tracking"
                                    role="tabpanel"
                                    aria-labelledby="tracking-tab"
                                    tabindex="0"
                                >
                                    <div class="row">
                                        <div class="col-sm-10">
                                            <div
                                                class="input-group mb-3"
                                                style="
                                                    border: 1px solid #00000021 !important;
                                                    border-radius: 5px;
                                                "
                                            >
                                                <div
                                                    class="input-group-prepend"
                                                >
                                                    <span
                                                        class="input-group-text bg-white h-100"
                                                        style="border: none"
                                                    >
                                                        <i
                                                            class="fa fa-history text-danger"
                                                        ></i>
                                                    </span>
                                                </div>
                                                <TextInput
                                                    type="text"
                                                    id="tracking_id"
                                                    class="form-control"
                                                    style="
                                                        border: none !important;
                                                    "
                                                    required=""
                                                    v-model="
                                                        trackingFormData.tracking_id
                                                    "
                                                    :placeholder="
                                                        $t('tracking_id')
                                                    "
                                                />
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <router-link
                                                :to="{
                                                    name: 'tracking_log',
                                                }"
                                                @click="trackingNow()"
                                                role="button"
                                                class="btn btn-lg btn-primary d-flex align-items-center justify-content-center"
                                                style="
                                                    padding: 0.3rem 1rem !important;
                                                    height: 45px;
                                                    border-radius: 6px;
                                                "
                                            >
                                                {{ $t("track_now") }}
                                            </router-link>
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
</template>
<script>
import TextInput from "@/Components/TextInput.vue";
import { onMounted, ref } from "vue";
import axios from "axios";
export default {
    setup() {
        const formData = ref({
            pickup_lat: "",
            pickup_long: "",
            pickup_location: "",
            pickup_location_show: "",
            drop_location: "",
            drop_latitude: "",
            drop_longitude: "",
        });

        const trackingFormData = ref({
            tracking_id: "",
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

        // Get users current location using the browser's geolocation API
        const getPickupLocation = () => {
            // Check if Geolocation is supported by the browser
            const isSupported =
                "navigator" in window && "geolocation" in navigator;
            if (isSupported) {
                // Retrieve the user's current position
                navigator.geolocation.getCurrentPosition((position) => {
                    pickupCoords.value.lat = position.coords.latitude;
                    pickupCoords.value.lng = position.coords.longitude;
                    // form data
                    formData.value.pickup_lat = position.coords.latitude;
                    formData.value.pickup_long = position.coords.longitude;
                    getStreetAddressFrom(
                        position.coords.latitude,
                        position.coords.longitude
                    );
                });
            }
        };

        const getStreetAddressFrom = (lat, long) => {
            const GOOGLE_MAPS_API_KEY = config.GOOGLE_MAPS_API_KEY;
            try {
                if (lat && long) {
                    axios
                        .get(
                            "https://maps.googleapis.com/maps/api/geocode/json?latlng=" +
                                lat +
                                "," +
                                long +
                                "&key=" +
                                GOOGLE_MAPS_API_KEY
                        )
                        .then((res) => {
                            if (res.data.results[0]) {
                                formData.value.pickup_location =
                                    res.data.results[0].formatted_address;
                                formData.value.pickup_location_show =
                                    res.data.results[0].formatted_address;
                            } else {
                                formData.value.pickup_location = "";
                                formData.value.pickup_location_show = "";
                            }
                        });
                }
            } catch (error) {
                console.log(error.message);
            }
        };

        const formDataStore = () => {
            localStorage.setItem("form_data", JSON.stringify(formData.value));
        };

        const trackingNow = () => {
            localStorage.setItem(
                "tracking_id",
                trackingFormData.value.tracking_id
            );
        };

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
        };

        onMounted(() => {
            getPickupLocation();
        });

        return {
            formData,
            trackingFormData,
            setPlace,
            setDropPlace,
            // pickup map
            pickupCoords,
            pickupMarkerDetails,
            pickupLocationDetails,
            getPickupLocation,
            formDataStore,
            trackingNow,
        };
    },
    components: {
        TextInput,
    },
};
</script>

<style lang="scss" scoped>
.hire-box {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 100%;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    overflow: hidden;
    position: relative;
    margin-top: -80px;
    z-index: 1;
}
.custom__tabs {
    .nav-pills {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        border: 1px solid rgba(var(--bs-primary-rgb), 0.2);
        border-radius: 100px;
        .nav-item {
            .nav-link {
                font-size: 18px;
                border-radius: 100px;
                padding: 6px 24px !important;
                margin: 0 !important;
                border: 1px solid transparent !important;
                background: transparent !important;
                color: var(--bs-primary);

                &.active {
                    background: var(--bs-primary) !important;
                    border: 1px solid var(--bs-primary);
                    color: #fff;
                }
            }
        }
    }
}
/* .nav-tabs {
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
.nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link {
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
.nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active {
    color: #FFFFFF !important;
    background-color: var(--bs-primary) !important;
}
#pills-tabs button.nav-link {
    padding: 8px 20px !important;
    margin: 0!important;
} */
</style>
