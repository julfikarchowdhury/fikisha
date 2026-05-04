import { axiosPrivate } from "./axiosInstance.js";

export default {
    // // Logout
    // logout() {
    //     return axiosPrivate.get("v1/auth/logout");
    // },

    // user() {
    //     return axiosPrivate.get("v1/auth/profile");
    // },

    // updateProfile(payload) {
    //     return axiosPrivate.post("v1/auth/profile/update", payload);
    // },

    // passwordChange(payload) {
    //     return axiosPrivate.post("v1/auth/password/change", payload);
    // },

    // // Permission
    // getPermissionData(payload) {
    //     return axiosPrivate.get("v1/permissions?page=" + payload);
    // },
    // storePermissionData(payload) {
    //     return axiosPrivate.post("v1/permissions", payload);
    // },
    // findPermissionData(payload) {
    //     return axiosPrivate.get("v1/permissions/" + payload);
    // },
    // updatePermissionData(payload) {
    //     return axiosPrivate.put("v1/permissions/" + payload.id, payload);
    // },
    // deletePermission(payload) {
    //     return axiosPrivate.delete("v1/permissions/" + payload);
    // },

    // // Role
    // getRoleData(payload) {
    //     return axiosPrivate.get("v1/roles?page=" + payload);
    // },
    // getAllPermissionsData() {
    //     return axiosPrivate.get("v1/get/all/permissions");
    // },
    // storeRoleData(payload) {
    //     return axiosPrivate.post("v1/roles", payload);
    // },
    // findRoleData(payload) {
    //     return axiosPrivate.get("v1/roles/" + payload);
    // },
    // updateRoleData(payload) {
    //     return axiosPrivate.put("v1/roles/" + payload.id, payload);
    // },
    // deleteRole(payload) {
    //     return axiosPrivate.delete("v1/roles/" + payload);
    // },

    // // User
    // getUserData(payload) {
    //     return axiosPrivate.get("v1/users?page=" + payload);
    // },
    // getAllRolesData() {
    //     return axiosPrivate.get("v1/get/all/roles");
    // },

    // storeUserData(payload) {
    //     return axiosPrivate.post("v1/users", payload);
    // },
    // findUserData(payload) {
    //     return axiosPrivate.get("v1/users/" + payload);
    // },
    // updateUserData(payload) {
    //     return axiosPrivate.put("v1/users/" + payload.id, payload);
    // },
    // deleteUser(payload) {
    //     return axiosPrivate.delete("v1/users/" + payload);
    // },

    // getCountryData() {
    //     return axiosPrivate.get("v1/get/countries");
    // },

    // // Category
    // getCategoryData(payload) {
    //     return axiosPrivate.get("v1/categories?page=" + payload);
    // },
    // getParentCategoryData() {
    //     return axiosPrivate.get("v1/get/parent/category");
    // },
    // storeCategoryData(payload) {
    //     return axiosPrivate.post("v1/categories", payload);
    // },
    // findCategoryData(payload) {
    //     return axiosPrivate.get("v1/categories/" + payload);
    // },
    // updateCategoryData(payload) {
    //     return axiosPrivate.put("v1/categories/" + payload.id, payload);
    // },
    // deleteCategory(payload) {
    //     return axiosPrivate.delete("v1/categories/" + payload);
    // },

    // // Todo
    // getTodoData(payload) {
    //     return axiosPrivate.get("v1/todos?page=" + payload);
    // },
    // storeTodoData(payload) {
    //     return axiosPrivate.post("v1/todos", payload);
    // },
    // findTodoData(payload) {
    //     return axiosPrivate.get("v1/todos/" + payload);
    // },
    // updateTodoData(payload) {
    //     return axiosPrivate.put("v1/todos/" + payload.id, payload);
    // },
    // deleteTodo(payload) {
    //     return axiosPrivate.delete("v1/todos/" + payload);
    // },
    // todoStatusUpdate(payload) {
    //     return axiosPrivate.get("v1/todo/status/update/" + payload);
    // },
    // getTodoUser() {
    //     return axiosPrivate.get("v1/get/todo/user");
    // },

    // // Language
    // getLanguageAllData() {
    //     return axiosPrivate.get("v1/get/languages");
    // },
    // getFlagsData() {
    //     return axiosPrivate.get("v1/get/flags");
    // },
    // getLangData(payload) {
    //     return axiosPrivate.get("v1/get/langs/" + payload);
    // },
    // getLanguageData(payload) {
    //     return axiosPrivate.get("v1/languages?page=" + payload);
    // },
    // storeLanguageData(payload) {
    //     return axiosPrivate.post("v1/languages", payload);
    // },
    // findLanguageData(payload) {
    //     return axiosPrivate.get("v1/languages/" + payload);
    // },
    // updateLanguageData(payload) {
    //     return axiosPrivate.put("v1/languages/" + payload.id, payload);
    // },
    // updateLangPhraseData(code, payload) {
    //     return axiosPrivate.post("v1/update/language/phrase/" + code, payload);
    // },
    // deleteLanguage(payload) {
    //     return axiosPrivate.delete("v1/languages/" + payload);
    // },

    // // Currency
    // getCurrencyAllData() {
    //     return axiosPrivate.get("v1/get/currencies");
    // },
    // getCurrencyData(payload) {
    //     return axiosPrivate.get("v1/currencies?page=" + payload);
    // },
    // storeCurrencyData(payload) {
    //     return axiosPrivate.post("v1/currencies", payload);
    // },
    // findCurrencyData(payload) {
    //     return axiosPrivate.get("v1/currencies/" + payload);
    // },
    // updateCurrencyData(payload) {
    //     return axiosPrivate.put("v1/currencies/" + payload.id, payload);
    // },
    // deleteCurrency(payload) {
    //     return axiosPrivate.delete("v1/currencies/" + payload);
    // },

    // // Setting
    // getGeneralSetting() {
    //     return axiosPrivate.get("v1/get/general_settings");
    // },
    // updateGeneralSetting(payload) {
    //     return axiosPrivate.post("v1/update/general_setting", payload);
    // },

    // //SMS Setting
    // getSmsSetting() {
    //     return axiosPrivate.get("v1/get/sms_settings");
    // },
    // updateSmsSetting(sms_method, payload) {
    //     return axiosPrivate.post("v1/update/sms_setting/" + sms_method, payload);
    // },

    // // Tax Rate
    // getTaxRateData(payload) {
    //     return axiosPrivate.get("v1/tax_rates?page=" + payload);
    // },
    // storeTaxRateData(payload) {
    //     return axiosPrivate.post("v1/tax_rates", payload);
    // },
    // findTaxRateData(payload) {
    //     return axiosPrivate.get("v1/tax_rates/" + payload);
    // },
    // updateTaxRateData(payload) {
    //     return axiosPrivate.put("v1/tax_rates/" + payload.id, payload);
    // },
    // deleteTaxRate(payload) {
    //     return axiosPrivate.delete("v1/tax_rates/" + payload);
    // },

    // // File Media
    // getFileMediaData(payload) {
    //     return axiosPrivate.get("v1/medias?page=" + payload);
    // },
    // storeFileMediaData(payload) {
    //     return axiosPrivate.post("v1/medias", payload);
    // },
    // findFileMediaData(payload) {
    //     return axiosPrivate.get("v1/medias/" + payload);
    // },
    // updateFileMediaData(payload) {
    //     return axiosPrivate.put("v1/medias/" + payload.id, payload);
    // },
    // deleteFileMedia(payload) {
    //     return axiosPrivate.delete("v1/medias/" + payload);
    // },
};
