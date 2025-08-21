export function formatPrice(currency = "THB", price = 0) {
    const numericPrice = isNaN(parseFloat(price)) ? 0 : parseFloat(price);
    const validCurrency = typeof currency === "string" && currency.trim() !== "" ? currency : "THB";

    return numericPrice.toLocaleString("th-TH", {
        style: "currency",
        currency: validCurrency,
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}

export function formatDateToDDMMYYYY(isoString) {
    if (isoString) {
        const date = new Date(isoString);
        const day = date.getDate().toString().padStart(2, '0');
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const year = date.getFullYear();
        const hours = date.getHours().toString().padStart(2, '0');
        const minutes = date.getMinutes().toString().padStart(2, '0');
        const seconds = date.getSeconds().toString().padStart(2, '0');
        return `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;
    } else {
        return null;
    }
}

export function formatDateToYYYYMMDD(isoString) {
    if (isoString) {
        const date = new Date(isoString);
        const year = date.getFullYear();
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const day = date.getDate().toString().padStart(2, '0');
        const hours = date.getHours().toString().padStart(2, '0');
        const minutes = date.getMinutes().toString().padStart(2, '0');
        const seconds = date.getSeconds().toString().padStart(2, '0');
        return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
    } else {
        return null;
    }
}

