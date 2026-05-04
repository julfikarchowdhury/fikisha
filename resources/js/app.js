import { createApp } from "vue";
import router from "@/router";
import App from "./App.vue";
import SkeletonLoader from "./components/SkeletonLoader.vue";
import Skeleton from "./components/Skeleton.vue";
import Loader from "./components/Loader.vue";
import i18n from "./lang";

//Imports the map package
import VueGoogleMaps from '@fawmi/vue-google-maps';

// i18n
import i18nLan from "./lang";
window.i18nLan = i18nLan;

// config
import config from "./config";
window.config = config;

window.$i18n = i18n;

const app = createApp(App);
app.use(router);
app.use(i18n);
app.use(VueGoogleMaps, {
    load: {
        key: config.GOOGLE_MAPS_API_KEY,  // Replace with your API Key
        libraries: 'places',  // Ensure 'places' library is loaded for Autocomplete
        // Use async and defer for better performance
        async: true,
        defer: true,
    },
});
app.component("skeleton-loader", SkeletonLoader);
app.component("skeleton", Skeleton);
app.component("loader", Loader);
app.config.errorHandler = () => null;
app.config.warnHandler = () => null;
app.config.devtools = false;
app.mount("#app");
