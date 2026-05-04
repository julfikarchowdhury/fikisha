import { createI18n } from "vue-i18n";
import vueLanguage from "../../../lang/vue-language.json"; 
   
const LanguageData = localStorage.getItem("locale_code") ? localStorage.getItem("locale_code") : "en";
export default createI18n({
    locale: LanguageData,
    fallbackLocale: LanguageData,
    messages: vueLanguage,
});


