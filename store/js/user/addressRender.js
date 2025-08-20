export const addressData = {
    "กรุงเทพมหานคร": {
        "เขตบางรัก": {
            "สีลม": "10500",
            "สุริยวงศ์": "10500"
        },
        "เขตวัฒนา": {
            "คลองเตยเหนือ": "10110",
            "คลองตันเหนือ": "10110"
        }
    },
    "เชียงใหม่": {
        "อำเภอเมืองเชียงใหม่": {
            "ช้างเผือก": "50300",
            "ศรีภูมิ": "50200"
        },
        "อำเภอแม่ริม": {
            "แม่ริม": "50180",
            "ริมใต้": "50180"
        }
    },
    "ภูเก็ต": {
        "อำเภอเมืองภูเก็ต": {
            "ตลาดใหญ่": "83000",
            "ตลาดเหนือ": "83000"
        },
        "อำเภอถลาง": {
            "เทพกระษัตรี": "83110",
            "เชิงทะเล": "83110"
        }
    }
};

const addressesContainer = document.getElementById('addressesContainer');
const addAddressCardBtn = document.getElementById('addAddressCardBtn');
const shippingAddressForm = document.getElementById('shippingAddressForm');
const messageBox = document.getElementById('messageBox');
const savedAddressesList = document.getElementById('savedAddressesList');
const copyAllAddressesBtn = document.getElementById('copyAllAddressesBtn');

let addressCardCount = 0;
const MAX_ADDRESS_CARDS = 3;

export function createAddressCard() {
    addressCardCount++;
    const cardIndex = addressCardCount;

    const addressCardDiv = document.createElement('div');
    addressCardDiv.className = 'address-card';
    addressCardDiv.id = `addressCard_${cardIndex}`;
    addressCardDiv.innerHTML = `
        <h3 class="address-card-title">ที่อยู่จัดส่งที่ ${cardIndex}</h3>
        <button type="button" class="remove-card-button" data-card-index="${cardIndex}">&times;</button>
        <div class="form-group">
            <label for="fullName_${cardIndex}">ชื่อ-นามสกุล</label>
            <input type="text" id="fullName_${cardIndex}" required>
        </div>
        <div class="form-group">
            <label for="phoneNumber_${cardIndex}">เบอร์โทรศัพท์</label>
            <input type="tel" id="phoneNumber_${cardIndex}" required>
        </div>
        <div class="form-group">
            <label for="addressLine1_${cardIndex}">ที่อยู่ 1</label>
            <input type="text" id="addressLine1_${cardIndex}" required>
        </div>
        <div class="form-group">
            <label for="addressLine2_${cardIndex}">ที่อยู่ 2 (ไม่บังคับ)</label>
            <input type="text" id="addressLine2_${cardIndex}">
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="province_${cardIndex}">จังหวัด</label>
                    <select id="province_${cardIndex}" required>
                        <option value="">เลือกจังหวัด</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="district_${cardIndex}">อำเภอ/เขต</label>
                    <select id="district_${cardIndex}" required disabled>
                        <option value="">เลือกอำเภอ/เขต</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="subDistrict_${cardIndex}">ตำบล/แขวง</label>
                    <select id="subDistrict_${cardIndex}" required disabled>
                        <option value="">เลือกตำบล/แขวง</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="postalCode_${cardIndex}">รหัสไปรษณีย์</label>
                    <input type="text" id="postalCode_${cardIndex}" readonly>
                </div>
            </div>
        </div>
    `;
    addressesContainer.appendChild(addressCardDiv);

    if (cardIndex > 1) {
        const copyButtonContainer = document.createElement('div');
        copyButtonContainer.className = 'd-flex justify-content-end mb-3';
        copyButtonContainer.innerHTML = `
            <button type="button" class="btn btn-info btn-sm" id="copyFromFirstBtn_${cardIndex}">
                คัดลอกข้อมูลจากที่อยู่ 1
            </button>
        `;
        addressCardDiv.insertBefore(copyButtonContainer, addressCardDiv.children[2]);
        const copyButton = document.getElementById(`copyFromFirstBtn_${cardIndex}`);
        copyButton.addEventListener('click', () => copyAddressData(cardIndex));
    }

    const provinceSelect = document.getElementById(`province_${cardIndex}`);
    const districtSelect = document.getElementById(`district_${cardIndex}`);
    const subDistrictSelect = document.getElementById(`subDistrict_${cardIndex}`);
    const postalCodeInput = document.getElementById(`postalCode_${cardIndex}`);
    const removeButton = addressCardDiv.querySelector('.remove-card-button');

    populateProvinces(provinceSelect);
    provinceSelect.addEventListener('change', () => populateDistricts(provinceSelect, districtSelect, subDistrictSelect, postalCodeInput));
    districtSelect.addEventListener('change', () => populateSubDistricts(provinceSelect, districtSelect, subDistrictSelect, postalCodeInput));
    subDistrictSelect.addEventListener('change', () => autoFillPostalCode(provinceSelect, districtSelect, subDistrictSelect, postalCodeInput));
    removeButton.addEventListener('click', () => removeAddressCard(cardIndex));

    if (addressCardCount >= MAX_ADDRESS_CARDS) {
        addAddressCardBtn.disabled = true;
    }
}

function populateProvinces(select) {
    select.innerHTML = '<option value="">เลือกจังหวัด</option>';
    for (const province in addressData) {
        const option = document.createElement('option');
        option.value = province;
        option.textContent = province;
        select.appendChild(option);
    }
}

function populateDistricts(provinceSelect, districtSelect, subDistrictSelect, postalCodeInput) {
    districtSelect.innerHTML = '<option value="">เลือกอำเภอ/เขต</option>';
    subDistrictSelect.innerHTML = '<option value="">เลือกตำบล/แขวง</option>';
    postalCodeInput.value = '';
    districtSelect.disabled = true;
    subDistrictSelect.disabled = true;

    const province = provinceSelect.value;
    if (province && addressData[province]) {
        for (const district in addressData[province]) {
            const option = document.createElement('option');
            option.value = district;
            option.textContent = district;
            districtSelect.appendChild(option);
        }
        districtSelect.disabled = false;
    }
}

function populateSubDistricts(provinceSelect, districtSelect, subDistrictSelect, postalCodeInput) {
    subDistrictSelect.innerHTML = '<option value="">เลือกตำบล/แขวง</option>';
    postalCodeInput.value = '';
    subDistrictSelect.disabled = true;

    const province = provinceSelect.value;
    const district = districtSelect.value;
    if (province && district && addressData[province] && addressData[province][district]) {
        for (const subDistrict in addressData[province][district]) {
            const option = document.createElement('option');
            option.value = subDistrict;
            option.textContent = subDistrict;
            subDistrictSelect.appendChild(option);
        }
        subDistrictSelect.disabled = false;
    }
}

function autoFillPostalCode(provinceSelect, districtSelect, subDistrictSelect, postalCodeInput) {
    const province = provinceSelect.value;
    const district = districtSelect.value;
    const subDistrict = subDistrictSelect.value;
    if (province && district && subDistrict && addressData[province]?.[district]?.[subDistrict]) {
        postalCodeInput.value = addressData[province][district][subDistrict];
    } else {
        postalCodeInput.value = '';
    }
}

function removeAddressCard(indexToRemove) {
    const card = document.getElementById(`addressCard_${indexToRemove}`);
    if (card) {
        addressesContainer.removeChild(card);
        addressCardCount--;
        if (addressCardCount < MAX_ADDRESS_CARDS) {
            addAddressCardBtn.disabled = false;
        }
        let currentCardIndex = 1;
        addressesContainer.querySelectorAll('.address-card').forEach(card => {
            card.querySelector('.address-card-title').textContent = `ที่อยู่จัดส่งที่ ${currentCardIndex}`;
            card.querySelector('.remove-card-button').dataset.cardIndex = currentCardIndex;
            currentCardIndex++;
        });
    }
}

function copyAddressData(targetIndex) {
    const fields = ['fullName', 'phoneNumber', 'addressLine1', 'addressLine2', 'province', 'district', 'subDistrict', 'postalCode'];
    fields.forEach(field => {
        const sourceField = document.getElementById(`${field}_1`);
        const targetField = document.getElementById(`${field}_${targetIndex}`);
        if (sourceField && targetField) {
            targetField.value = sourceField.value;
            if (field === 'province') populateDistricts(targetField, document.getElementById(`district_${targetIndex}`), document.getElementById(`subDistrict_${targetIndex}`), document.getElementById(`postalCode_${targetIndex}`));
            if (field === 'district') populateSubDistricts(document.getElementById(`province_${targetIndex}`), targetField, document.getElementById(`subDistrict_${targetIndex}`), document.getElementById(`postalCode_${targetIndex}`));
            if (field === 'subDistrict') autoFillPostalCode(document.getElementById(`province_${targetIndex}`), document.getElementById(`district_${targetIndex}`), targetField, document.getElementById(`postalCode_${targetIndex}`));
        }
    });
}
