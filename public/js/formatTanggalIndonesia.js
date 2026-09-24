/**
 * Format tanggal Indonesia (tanpa andalkan locale OS id-ID).
 * Contoh: Kamis, 24 September 2026
 */
(function (global) {
    const DAYS = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const MONTHS = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
    ];

    function parseDate(value) {
        if (value instanceof Date) {
            return Number.isNaN(value.getTime()) ? null : value;
        }
        if (value === null || value === undefined || value === '' || value === '0000-00-00' || value === '0000-00-00 00:00:00') {
            return null;
        }
        const parsed = new Date(value);
        return Number.isNaN(parsed.getTime()) ? null : parsed;
    }

    function pad2(n) {
        return String(n).padStart(2, '0');
    }

    /**
     * @param {Date|string|number} value
     * @param {{withTime?: boolean}} [options]
     * @returns {string}
     */
    function formatTanggalIndonesia(value, options) {
        const date = parseDate(value);
        if (!date) {
            return '-';
        }

        const withTime = !!(options && options.withTime);
        let text = DAYS[date.getDay()] + ', ' + date.getDate() + ' ' + MONTHS[date.getMonth()] + ' ' + date.getFullYear();
        if (withTime) {
            text += ' ' + pad2(date.getHours()) + ':' + pad2(date.getMinutes());
        }

        return text;
    }

    global.formatTanggalIndonesia = formatTanggalIndonesia;
    global.HARI_INDONESIA = DAYS;
    global.BULAN_INDONESIA = MONTHS;
})(typeof window !== 'undefined' ? window : globalThis);
