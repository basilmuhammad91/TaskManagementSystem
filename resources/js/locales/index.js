import Vue from 'vue';
import VueI18n from 'vue-i18n';

import en from "./en/index";
import fr from "./fr/index";

Vue.use(VueI18n);

const messages = {
    en: en,
    fr: fr
}

const lang = document.head.querySelector("[name~=locale][content]").content;
window.currentLanguage=lang;

export default new VueI18n({
    locale: lang,
    messages
});