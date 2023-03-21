import { createI18n } from 'vue-i18n'
import ar from './messages-ar'
import en from './messages-en'

export const languages = [
    {
        label: 'English',
        locale: 'en'
    },
    {
        label: 'العربية',
        locale: 'ar'
    } 
]
export const defaultLang = 'en'

export const i18n = createI18n({
    legacy: false,
    locale: defaultLang,
    messages: {
        en,
        ar
    }
})

export function setLocale(lang) {
    i18n.global.locale.value = lang
    // setDocumentAttributesFor(lang)
}