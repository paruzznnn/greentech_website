
export async function fetchAddressData(req, call) {
    try {
        const params = new URLSearchParams({
            action: req
        });
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
        return await response.json();
    } catch (error) {
        console.error('Fetch error:', error);
        return { data: [] };
    }
}

export async function fetchProvincesData(call) {
    try {
        const url = call;
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
        return await response.json();
    } catch (error) {
        console.error('Fetch error:', error);
        return { data: [] };
    }
}

export async function fetchDistrictsData(call) {
    try {
        const url = call;
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
        return await response.json();
    } catch (error) {
        console.error('Fetch error:', error);
        return { data: [] };
    }
}

export async function fetchSubdistricts(call) {
    try {
        const url = call;
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
        return await response.json();
    } catch (error) {
        console.error('Fetch error:', error);
        return { data: [] };
    }
}

export const AddressUI = {
    lang: null,
    addressCardCount: 0,
    MAX_ADDRESS_CARDS: 3,

    addressData: [],
    provincesData: [],
    districtsData: [],
    subdistrictsData: [],

    selectors: {
        addressesContainer: document.getElementById('addressesContainer'),
        addAddressCardBtn: document.getElementById('addAddressCardBtn'),
        confirmAddress: document.getElementById('confirmAddress'),

        //====== Default Shipping Address =================================
        selectedFullname: null,
        selectedPhoneNumber: null,
        selectedAddressDetail: null,

        selectedProvince: null,
        selectedDistrict: null,
        selectedSubdistrict: null,
        selectedPostalCode: null
    },

    init(provinces, districts, subdistricts, address) {
        this.lang = 'th';

        this.addressData = address;
        this.provincesData = provinces;
        this.districtsData = districts;
        this.subdistrictsData = subdistricts;

        this.initEvents();

        if (!address?.data || address.data.length === 0) {
            this.createAddressCard();
        } else {
            this.loadAddressCard();
        }
    },

    loadAddressCard() {
        const addressItems = this.addressData?.data || [];
        for (const item of addressItems) {
            this.createAddressCard(item);
        }
    },

    createAddressCard(item = null) {
        this.addressCardCount++;
        const currentCardIndex = this.addressCardCount;

        const addressCardDiv = document.createElement('div');
        addressCardDiv.className = 'address-card';
        addressCardDiv.id = `addressCard_${currentCardIndex}`;
        addressCardDiv.innerHTML = `
            <h5 class="address-card-title">ที่อยู่จัดส่งที่ ${currentCardIndex}</h5>
            <input type="hidden" name="addressID_${currentCardIndex}" value="${item?.address_id || 0}" />
            <input type="hidden" name="addressRemove_${currentCardIndex}" value="0" />
            <input type="hidden" name="setupShipping_${currentCardIndex}" value="0" />
            <div style="display: flex; align-items: center; gap: 8px; justify-content: flex-end;">
                <span style="font-size: 0.8rem;">ตั้งค่าเริ่มต้น</span>
                <label class="toggle-switch">
                    <input type="checkbox" 
                        class="setupShippingCheckbox" 
                        id="setupShipping_${currentCardIndex}" 
                        name="setupShipping_${currentCardIndex}"
                        value="1"
                        data-card-index="${currentCardIndex}"
                        ${item?.status ? 'checked' : ''} />
                    <span class="slider"></span>
                </label>
            </div>
            <button type="button" class="remove-card-button" data-card-index="${currentCardIndex}">&times;</button>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="full_name_${currentCardIndex}">ชื่อ-นามสกุล</label>
                        <input type="text" id="full_name_${currentCardIndex}" name="full_name_${currentCardIndex}" 
                            value="${item?.full_name || ''}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="phone_number_${currentCardIndex}">เบอร์โทรศัพท์</label>
                        <input type="tel" id="phone_number_${currentCardIndex}" name="phone_number_${currentCardIndex}" 
                            value="${item?.phone_number || ''}" required>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="address_detail_${currentCardIndex}">รายละเอียดที่อยู่ ${currentCardIndex}</label>
                <textarea id="address_detail_${currentCardIndex}" name="address_detail_${currentCardIndex}" required>${item?.address_detail || ''}</textarea>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="province_${currentCardIndex}">จังหวัด</label>
                        <select id="province_${currentCardIndex}" name="province_${currentCardIndex}" required>
                            <option value="">เลือกจังหวัด</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="district_${currentCardIndex}">อำเภอ/เขต</label>
                        <select id="district_${currentCardIndex}" name="district_${currentCardIndex}" required disabled>
                            <option value="">เลือกอำเภอ/เขต</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="subdistrict_${currentCardIndex}">ตำบล/แขวง</label>
                        <select id="subdistrict_${currentCardIndex}" name="subdistrict_${currentCardIndex}" required disabled>
                            <option value="">เลือกตำบล/แขวง</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="postalCode_${currentCardIndex}">รหัสไปรษณีย์</label>
                        <input type="text" id="postalCode_${currentCardIndex}" name="postalCode_${currentCardIndex}" readonly>
                    </div>
                </div>
            </div>
        `;
        this.selectors.addressesContainer.appendChild(addressCardDiv);

        this.selectors.selectedProvince = document.getElementById(`province_${currentCardIndex}`);
        this.selectors.selectedDistrict = document.getElementById(`district_${currentCardIndex}`);
        this.selectors.selectedSubdistrict = document.getElementById(`subdistrict_${currentCardIndex}`);
        this.selectors.selectedPostalCode = document.getElementById(`postalCode_${currentCardIndex}`);

        // ===== LOAD DATA =====
        this.populateProvinces();

        if (item) {
            if (item.province_code) {
                this.provinceActive = item.province_code;
                this.selectors.selectedProvince.value = item.province_code;
                this.populateDistricts();

                if (item.district_code) {
                    this.districtActive = item.district_code;
                    this.selectors.selectedDistrict.value = item.district_code;
                    this.populateSubDistricts();

                    if (item.subdistrict_code) {
                        this.selectors.selectedSubdistrict.value = item.subdistrict_code;

                        if (item.post_code) {
                            this.postalCodeActive = item.post_code;
                            this.populatePostalCode();
                        }
                    }
                }
            }
        } else {
            this.populateDistricts();
            this.populateSubDistricts();
        }

        // เพิ่ม event สำหรับปุ่มลบ
        const removeButton = addressCardDiv.querySelector('.remove-card-button');
        removeButton.addEventListener('click', () => this.removeAddressCard(currentCardIndex));

        // อัพเดทการแสดงปุ่มลบทั้งหมด
        this.updateRemoveButtons();

        if (this.getVisibleCardCount() >= this.MAX_ADDRESS_CARDS) {
            this.selectors.addAddressCardBtn.disabled = true;
        }
    },

    populateProvinces() {
        const provinceOption = this.provincesData.map(item => {
            const provinceName = this.lang === 'en' ? item.provinceNameEn : item.provinceNameTh;
            return `<option value="${item.provinceCode}" data-code="${item.provinceCode}">${provinceName}</option>`;
        });
        const defaultOption = `<option value="">${this.lang === 'en' ? 'Select Province' : 'เลือกจังหวัด'}</option>`;
        this.selectors.selectedProvince.innerHTML = defaultOption + provinceOption.join('');
    },

    populateDistricts() {
        if (this.provinceActive) {
            this.selectors.selectedDistrict.removeAttribute("disabled");
        } else {
            this.selectors.selectedDistrict.setAttribute("disabled", true);
        }
        const districtOption = this.districtsData
            .filter(item => item.provinceCode == this.provinceActive)
            .map(item => {
                const districtName = this.lang === 'en' ? item.districtNameEn : item.districtNameTh;
                return `<option value="${item.districtCode}" data-code="${item.districtCode}">${districtName}</option>`;
            });
        const defaultOption = `<option value="">${this.lang === 'en' ? 'Select District' : 'เลือกอำเภอ/เขต'}</option>`;
        this.selectors.selectedDistrict.innerHTML = defaultOption + districtOption.join('');
    },

    populateSubDistricts() {
        if (this.districtActive) {
            this.selectors.selectedSubdistrict.removeAttribute("disabled");
        } else {
            this.selectors.selectedSubdistrict.setAttribute("disabled", true);
        }
        const subdistrictOption = this.subdistrictsData
            .filter(item => item.districtCode == this.districtActive)
            .map(item => {
                const subdistrictName = this.lang === 'en' ? item.subdistrictNameEn : item.subdistrictNameTh;
                return `<option value="${item.subdistrictCode}" data-code="${item.postalCode}">${subdistrictName}</option>`;
            });
        const defaultOption = `<option value="">${this.lang === 'en' ? 'Select Subdistrict' : 'เลือกตำบล/แขวง'}</option>`;
        this.selectors.selectedSubdistrict.innerHTML = defaultOption + subdistrictOption.join('');
    },

    populatePostalCode() {
        this.selectors.selectedPostalCode.value = this.postalCodeActive;
    },

    // ฟังก์ชันสำหรับอัพเดทการแสดงปุ่มลบ
    updateRemoveButtons() {
        const visibleCards = this.selectors.addressesContainer.querySelectorAll('.address-card:not([style*="display: none"])');

        visibleCards.forEach(card => {
            const removeButton = card.querySelector('.remove-card-button');
            if (visibleCards.length <= 1) {
                removeButton.style.display = 'none';
            } else {
                removeButton.style.display = 'block';
            }
        });
    },

    // ฟังก์ชันสำหรับนับจำนวน card ที่แสดงอยู่
    getVisibleCardCount() {
        return this.selectors.addressesContainer.querySelectorAll('.address-card:not([style*="display: none"])').length;
    },

    removeAddressCard(indexToRemove) {
        // นับจำนวน card ที่ยังแสดงอยู่
        const visibleCardCount = this.getVisibleCardCount();

        // ถ้าเหลือ card เดียว ไม่ให้ลบ
        if (visibleCardCount <= 1) {
            alert('ต้องมีที่อยู่อย่างน้อย 1 ที่อยู่');
            return;
        }

        const card = document.getElementById(`addressCard_${indexToRemove}`);
        if (card) {
            const removeInput = card.querySelector(`input[name="addressRemove_${indexToRemove}"]`);
            if (removeInput) {
                removeInput.value = "1";
            }
            card.style.display = 'none';

            if (this.getVisibleCardCount() < this.MAX_ADDRESS_CARDS) {
                this.selectors.addAddressCardBtn.disabled = false;
            }

            // อัพเดทหมายเลข card ที่แสดงอยู่
            let currentCardIndex = 1;
            this.selectors.addressesContainer.querySelectorAll('.address-card').forEach((cardElement) => {
                if (cardElement.style.display !== 'none') {
                    cardElement.querySelector('.address-card-title').textContent = `ที่อยู่จัดส่งที่ ${currentCardIndex}`;
                    currentCardIndex++;
                }
            });

            // อัพเดทการแสดงปุ่มลบ
            this.updateRemoveButtons();
        }
    },

    initEvents() {
        document.addEventListener('click', (event) => {
            if (event.target.closest('#addAddressCardBtn')) {
                this.createAddressCard();
            }
        });

        document.addEventListener('change', (event) => {
            // หา cardIndex จาก element ID
            const elementId = event.target.id;
            const cardIndex = elementId.match(/_(\d+)$/)?.[1];
            if (!cardIndex) return;

            if (event.target.classList.contains('setupShippingCheckbox')) {
                const checkbox = event.target;
                if (checkbox.checked) {
                    document.querySelectorAll('.setupShippingCheckbox').forEach(cb => {
                        if (cb !== checkbox) {
                            cb.checked = false;
                        }
                    });
                }
            }

            if (elementId.startsWith('province_')) {
                const selectedOption = event.target.options[event.target.selectedIndex];
                const value = selectedOption.value;
                const dataCode = selectedOption.dataset.code;

                this.selectors.selectedProvince = document.getElementById(`province_${cardIndex}`);
                this.selectors.selectedDistrict = document.getElementById(`district_${cardIndex}`);
                this.selectors.selectedSubdistrict = document.getElementById(`subdistrict_${cardIndex}`);
                this.selectors.selectedPostalCode = document.getElementById(`postalCode_${cardIndex}`);

                this.provinceActive = dataCode;
                this.districtActive = null;
                this.postalCodeActive = null;
                this.populateDistricts();
                this.populateSubDistricts();
                this.populatePostalCode();
            }

            if (elementId.startsWith('district_')) {
                const selectedOption = event.target.options[event.target.selectedIndex];
                const value = selectedOption.value;
                const dataCode = selectedOption.dataset.code;

                this.selectors.selectedDistrict = document.getElementById(`district_${cardIndex}`);
                this.selectors.selectedSubdistrict = document.getElementById(`subdistrict_${cardIndex}`);
                this.selectors.selectedPostalCode = document.getElementById(`postalCode_${cardIndex}`);

                this.districtActive = dataCode;
                this.postalCodeActive = null;
                this.populateSubDistricts();
                this.populatePostalCode();
            }

            if (elementId.startsWith('subdistrict_')) {
                const selectedOption = event.target.options[event.target.selectedIndex];
                const value = selectedOption.value;
                const dataCode = selectedOption.dataset.code;

                this.selectors.selectedPostalCode = document.getElementById(`postalCode_${cardIndex}`);

                this.postalCodeActive = dataCode;
                this.populatePostalCode();
            }
        });
    }
};

