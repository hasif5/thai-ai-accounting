import { format } from 'date-fns';
import { th, enUS } from 'date-fns/locale';

/**
 * Format a date using the specified locale format
 * 
 * @param {string|Date} date - The date to format
 * @param {string} formatString - Optional date format string
 * @param {string} locale - Locale to use for formatting ('th' or 'en')
 * @returns {string} - Formatted date string
 */
export function formatDate(date, formatString = 'dd MMM yyyy', locale = 'th') {
  if (!date) return '';
  
  try {
    const dateObj = date instanceof Date ? date : new Date(date);
    const localeObj = locale === 'th' ? th : enUS;
    
    // For Thai locale, add 543 years to convert to Buddhist Era
    if (locale === 'th') {
      const thaiYear = dateObj.getFullYear() + 543;
      return format(dateObj, formatString, { locale: localeObj })
        .replace(dateObj.getFullYear().toString(), thaiYear.toString());
    }
    
    return format(dateObj, formatString, { locale: localeObj });
  } catch (error) {
    console.error('Error formatting date:', error);
    return '';
  }
}

/**
 * Format a currency value with Thai baht symbol and separators
 * 
 * @param {number} amount - The amount to format
 * @param {string} locale - Locale to use for formatting ('th' or 'en')
 * @param {boolean} includeCurrency - Whether to include the currency symbol
 * @returns {string} - Formatted currency string
 */
export function formatCurrency(amount, locale = 'th', includeCurrency = true) {
  if (amount === undefined || amount === null) return includeCurrency ? '฿0.00' : '0.00';
  
  try {
    const formatter = new Intl.NumberFormat(locale === 'th' ? 'th-TH' : 'en-US', {
      style: includeCurrency ? 'currency' : 'decimal',
      currency: 'THB',
      currencyDisplay: 'symbol',
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    });
    
    return formatter.format(amount);
  } catch (error) {
    console.error('Error formatting currency:', error);
    return includeCurrency ? '฿0.00' : '0.00';
  }
}

/**
 * Format a number with thousands separators
 * 
 * @param {number} value - The number to format
 * @param {string} locale - Locale to use for formatting ('th' or 'en')
 * @returns {string} - Formatted number string
 */
export function formatNumber(value, locale = 'th') {
  if (value === undefined || value === null) return '0';
  
  try {
    const formatter = new Intl.NumberFormat(locale === 'th' ? 'th-TH' : 'en-US');
    return formatter.format(value);
  } catch (error) {
    console.error('Error formatting number:', error);
    return '0';
  }
} 