import { createI18n } from 'vue-i18n';
import th from './locales/th';
import en from './locales/en';
import { formatDate, formatCurrency } from './filters';

const i18n = createI18n({
  legacy: false, // Use Composition API mode
  locale: 'th', // Default locale
  fallbackLocale: 'en',
  messages: {
    th,
    en
  },
  numberFormats: {
    'th': {
      currency: {
        style: 'currency',
        currency: 'THB',
        currencyDisplay: 'symbol',
        minimumFractionDigits: 2
      }
    },
    'en': {
      currency: {
        style: 'currency',
        currency: 'THB',
        currencyDisplay: 'symbol',
        minimumFractionDigits: 2
      }
    }
  },
  datetimeFormats: {
    'th': {
      short: {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      },
      long: {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        weekday: 'long'
      }
    },
    'en': {
      short: {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      },
      long: {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        weekday: 'long'
      }
    }
  }
});

export { formatDate, formatCurrency };
export default i18n; 