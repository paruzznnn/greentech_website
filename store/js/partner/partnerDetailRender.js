// ---------- API PARTNER -----------------------------
export async function fetchPartnerData(req, call) {
    try {
        const params = new URLSearchParams({ action: req });
        const url = call + params.toString();

        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer my_secure_token_123',
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const res = await response.json();
        const data = res.data || [];

        return data;
    } catch (error) {
        console.error('Fetch error:', error);
        return { data: [] };
    }
}

//---------- RENDER PARTNER ---------------------------------


