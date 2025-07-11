function AddressForm(language) {
    let currentLang = language || 'US';

    function init() {
        loadPrefix(currentLang);
        loadProvinces(currentLang);
        loadDistricts(currentLang);
        loadSubdistricts(currentLang);
        loadPostalCodes(currentLang);
        loadNation();
        bindEvents();
    }

    function bindEvents() {
        $('#language-select').on('change', () => {
            currentLang = $('#language-select').val() ?? 'US';
            loadPrefix(currentLang);
            loadProvinces(currentLang);
            loadDistricts(currentLang);
            loadSubdistricts(currentLang);
        });

        $('#province').on('change', () => {
            const provinceCode = $('#province').val();
            loadDistricts(currentLang, provinceCode);
            loadSubdistricts(currentLang, provinceCode);
            loadPostalCodes(currentLang, provinceCode);
        });

        $('#district').on('change', () => {
            const provCode = $('#district').find('option:selected').data('prov');
            const distCode = $('#district').find('option:selected').data('dist');
            loadProvinces(currentLang, provCode);
            loadSubdistricts(currentLang, provCode, distCode);
            loadPostalCodes(currentLang, provCode, distCode);
        });

        $('#subdistrict').on('change', () => {
            const provCode = $('#subdistrict').find('option:selected').data('prov');
            const distCode = $('#subdistrict').find('option:selected').data('dist');
            const subdCode = $('#subdistrict').val();
            loadProvinces(currentLang, provCode);
            loadDistricts(currentLang, provCode, distCode);
            loadPostalCodes(currentLang, provCode, distCode, subdCode);
        });

        $('#post_code').on('change', () => {
            const provCode = $('#post_code').find('option:selected').data('prov');
            const distCode = $('#post_code').find('option:selected').data('dist');
            loadProvinces(currentLang, provCode);
            loadDistricts(currentLang, provCode, distCode);
            loadSubdistricts(currentLang, provCode, distCode);
        });

        $(document).on('click', '.cancel-btn', (event) => {
            const dataID = $(event.target).data('id');
            resetForm(dataID);
        });
    }

    function resetForm(dataID) {
        switch (dataID) {
            case 'dt':
            case 'sdt':
            default:
                loadProvinces(currentLang);
                loadDistricts(currentLang);
                loadSubdistricts(currentLang);
                loadPostalCodes(currentLang);
                break;
        }
    }

    function loadNation(){
        $.getJSON("../api/languages/nation.json" + '?' + new Date().getTime(), function(data) {

            let nationalities = data.nationalities;
            let $select = $('#country');
            $select.empty();
            
            let filteredNationalities = nationalities.filter(function(entry) {
                return entry.abbreviation === 'TH';
            });
        
            $.each(filteredNationalities, function(key, entry) {
                let option = $('<option></option>')
                    .attr('value', entry.abbreviation)
                    .attr('data-flag', entry.flag) 
                    .text(entry.name);
                
                $select.append(option);
            });
        
            if (filteredNationalities.length > 0) {
                $select.select2({
                    templateResult: formatState,
                    templateSelection: formatState
                });
            } else {
                console.error("ไม่มีข้อมูลสำหรับสร้าง options");
            }
        });
    }

    function loadPrefix(language) {
        $.getJSON("../api/person/personal.json" + '?' + new Date().getTime(), (data) => {
            let $select = $('#prefix');
            $select.empty();
            data.forEach((entry) => {
                let prefixName = (language === 'TH') ? entry.prefixTh : entry.prefixEn;
                $select.append($('<option></option>').attr('value', entry.id).text(prefixName));
            });

            if (data.length > 0) {
                $select.select2();
            } else {
                console.error("ไม่มีข้อมูลสำหรับสร้าง options");
            }
        });
    }

    function loadProvinces(language, code = '') {
        $.getJSON("../api/address/provinces.json" + '?' + new Date().getTime(), (data) => {
            let $select = $('#province');
            $select.empty();
            let filteredData = code ? data.filter(entry => entry.provinceCode === parseInt(code)) : data;
            filteredData.forEach((entry) => {
                let provinceName = (language === 'TH') ? entry.provinceNameTh : entry.provinceNameEn;
                $select.append($('<option></option>')
                    .attr('value', entry.provinceCode)
                    .text(provinceName));
            });

            if (filteredData.length > 0) {
                $select.select2({
                    templateResult: templateAddress,
                    templateSelection: templateAddress
                });
            } else {
                console.error("ไม่มีข้อมูลสำหรับสร้าง options");
            }
        });
    }

    function loadDistricts(language, provCode = '', distCode = '') {
        $.getJSON("../api/address/districts.json" + '?' + new Date().getTime(), (data) => {
            let $select = $('#district');
            $select.empty();
            let filteredData = provCode ? data.filter(entry => entry.provinceCode === parseInt(provCode)) : data;
            let filteredDist = distCode ? filteredData.filter(entry => entry.districtCode === parseInt(distCode)) : filteredData;

            filteredDist.forEach((entry) => {
                let districtName = (language === 'TH') ? entry.districtNameTh : entry.districtNameEn;
                let $option = $('<option></option>')
                    .attr('value', entry.districtCode)
                    .attr('data-prov', entry.provinceCode)
                    .attr('data-dist', entry.districtCode)
                    .attr('data-type', 'dt')
                    .text(districtName);
                $select.append($option);
            });

            if (filteredDist.length > 0) {
                $select.select2({
                    templateResult: templateAddress,
                    templateSelection: templateAddress
                });
            } else {
                console.error("ไม่มีข้อมูลสำหรับสร้าง options");
            }
        });
    }

    function loadSubdistricts(language, provCode = '', distCode = '') {
        $.getJSON("../api/address/subdistricts.json" + '?' + new Date().getTime(), (data) => {
            let $select = $('#subdistrict');
            $select.empty();
            let filteredData = provCode ? data.filter(entry => entry.provinceCode === parseInt(provCode)) : data;
            let filteredSubd = distCode ? filteredData.filter(entry => entry.districtCode === parseInt(distCode)) : filteredData;

            filteredSubd.forEach((entry) => {
                let subdistrictName = (language === 'TH') ? entry.subdistrictNameTh : entry.subdistrictNameEn;
                $select.append($('<option></option>')
                    .attr('value', entry.subdistrictCode)
                    .attr('data-prov', entry.provinceCode)
                    .attr('data-dist', entry.districtCode)
                    .attr('data-type', 'sdt')
                    .text(subdistrictName));
            });

            if (filteredSubd.length > 0) {
                $select.select2({
                    templateResult: templateAddress,
                    templateSelection: templateAddress
                });
            } else {
                console.error("ไม่มีข้อมูลสำหรับสร้าง options");
            }
        });
    }

    function loadPostalCodes(language, provCode = '', distCode = '', subdCode = '') {
        $.getJSON("../api/address/districts.json" + '?' + new Date().getTime(), (data) => {
            let $select = $('#post_code');
            $select.empty();
            let filteredData = provCode ? data.filter(entry => entry.provinceCode === parseInt(provCode)) : data;
            let filteredSubd = distCode ? filteredData.filter(entry => entry.districtCode === parseInt(distCode)) : filteredData;

            filteredSubd.forEach((entry) => {
                let postalCodeName = (language === 'TH') ? entry.postalCode : entry.postalCode;
                $select.append($('<option></option>')
                    .attr('value', entry.postalCode)
                    .attr('data-prov', entry.provinceCode)
                    .attr('data-dist', entry.districtCode)
                    .text(postalCodeName));
            });

            if (filteredSubd.length > 0) {
                $select.select2({
                    templateResult: templateAddress,
                    templateSelection: templateAddress
                });
            } else {
                console.error("ไม่มีข้อมูลสำหรับสร้าง options");
            }
        });
    }

    function templateAddress(state) {
        if (!state.id) {
            return state.text;
        }

        var dataType = $(state.element).data('type') || '';
        var $state = $(
            `<span>
                ${state.text}
                <button type="button" data-id="${dataType}" class="ml-auto cancel-btn remove-btn">
                    <i class="fas fa-times" font-size: 12px;></i>
                </button>
            </span>`
        );

        return $state;
    }

    function formatState(state) {
        if (!state.id) {
            return state.text;
        }

        var flagUrl = $(state.element).data('flag');
        var $state = $(
            '<span><img src="' + flagUrl + '" class="img-flag" style="width:20px; margin-right: 10px;" /> ' + state.text + '</span>'
        );
        return $state;
    }

    // Initialize the form
    init();
}
// Usage
AddressForm('TH');


// function setFormAddress(language, elementId, elementName, elementKey) {
//     let currentLang = language || 'US';
//     let numberId = elementId.replace(/[^0-9]/g, '');

//     function setInit() {
//         switch (elementName) {
//             case 'prefix':
//                 setLoadPrefix(currentLang, elementId, elementKey);
//                 break;
//             case 'country':
//                 setLoadNation(language, elementId, elementKey);
//                 break;
//             case 'province':
//                 setLoadProvinces(currentLang, elementId, elementKey);
//                 break;
//             case 'district':
//                 setLoadDistricts(currentLang, elementId, elementKey);

//                 break;
//             case 'subdistrict':
//                 setLoadSubdistricts(currentLang, elementId, elementKey);
//                 break;
//             case 'post_code':
//                 setLoadPostalCodes(currentLang, elementId, elementKey);
//                 break;
//             default:
//                 break;
//         }

//         setbindEvents();
//     }

//     function setbindEvents() {
//         $('#language-select').on('change', () => {
//             currentLang = $('#language-select').val() ?? 'US';

//             switch (elementName) {
//                 case 'prefix':
//                     setLoadPrefix(currentLang, 'prefix_' + numberId, elementKey);
//                     break;
//                 case 'country':
//                     setLoadNation(language, elementId, elementKey);
//                     break;
//                 case 'province':
//                     setLoadProvinces(currentLang, 'province_' + numberId, elementKey);
//                     break;
//                 case 'district':
//                     setLoadDistricts(currentLang, 'district_' + numberId, elementKey);
//                     break;
//                 case 'subdistrict':
//                     setLoadSubdistricts(currentLang, 'subdistrict_' + numberId, elementKey);
//                     break;
//                 case 'post_code':
//                     setLoadPostalCodes(currentLang, 'post_code_' + numberId, elementKey);
//                     break;
//                 default:
//                     break;
//             }
            
//         });

//         switch (elementName) {
//             case 'province':

//                 $(document).on('change', '#province_' + numberId, function(event) {
//                     const provinceCode = $(this).val();
//                     setLoadDistricts(currentLang, 'district_' + numberId, null, provinceCode);
//                     setLoadPostalCodes(currentLang, 'post_code_' + numberId, null, provinceCode);
//                 });

//                 break;
//             case 'district':

//                 $(document).on('change', '#district_' + numberId, function(event) {
//                     const provCode = $(this).find('option:selected').data('prov');
//                     const distCode = $(this).find('option:selected').data('dist');
                    
//                     setLoadProvinces(currentLang, 'province_' + numberId, null, provCode);
//                     setLoadPostalCodes(currentLang, 'post_code_' + numberId, null, provCode, distCode);
//                 });
                
//                 break;
//             case 'subdistrict':

//                 $(document).on('change', '#subdistrict_' + numberId, function(event) {
//                     const provCode = $(this).find('option:selected').data('prov');
//                     const distCode = $(this).find('option:selected').data('dist');
//                     const subdCode = $(this).val();

//                     setLoadProvinces(currentLang, 'province_' + numberId, null, provCode);
//                     setLoadDistricts(currentLang, 'district_' + numberId, null, provCode, distCode);
//                     setLoadPostalCodes(currentLang, 'post_code_' + numberId, null, provCode, distCode);
//                 });
                
//                 break;
//             case 'post_code':

//                 $(document).on('change', '#post_code_' + numberId, function(event) {
//                     const provCode = $(this).find('option:selected').data('prov');
//                     const distCode = $(this).find('option:selected').data('dist');
                    
//                     setLoadProvinces(currentLang, 'province_' + numberId, null, provCode);
//                     setLoadDistricts(currentLang, 'district_' + numberId, null, provCode, distCode);
//                 });
                
//                 break;
//             default:
//                 break;
//         }


//         $(document).on('click', '.cancel-address', (event) => {
//             const dataID = $(event.target).data('id');
//             resetFormSet(dataID);
//         });
//     }

//     function resetFormSet(dataID) {
//         switch (dataID) {
//             case 'dt':
//             case 'sdt':
//             default:
//                 setLoadPrefix(currentLang, 'prefix_' + numberId, null);
//                 setLoadProvinces(currentLang, 'province_' + numberId, null);
//                 setLoadDistricts(currentLang, 'district_' + numberId, null);
//                 setLoadSubdistricts(currentLang, 'subdistrict_' + numberId, null);
//                 setLoadPostalCodes(currentLang, 'post_code_' + numberId, null);
//                 break;
//         }
//     }

//     function setLoadNation(language, elementId, elementKey){

//         $.getJSON("../api/languages/nation.json" + '?' + new Date().getTime(), function(data) {

//             let nationalities = data.nationalities;
//             let $select = $('#'+elementId);
//             $select.empty();
            
//             let filteredNationalities = nationalities.filter(function(entry) {
//                 return entry.abbreviation === 'TH';
//             });
        
//             $.each(filteredNationalities, function(key, entry) {
//                 let option = $('<option></option>')
//                     .attr('value', entry.abbreviation)
//                     .attr('data-flag', entry.flag) 
//                     .text(entry.name);
                
//                 $select.append(option);
//             });
        
//             if (filteredNationalities.length > 0) {
//                 $select.select2({
//                     templateResult: setFormatState,
//                     templateSelection: setFormatState
//                 });
//             } else {
//                 console.error("ไม่มีข้อมูลสำหรับสร้าง options");
//             }
//         });
//     }


//     function setLoadPrefix(language, elementId, elementKey) {
//         $.getJSON("../api/person/personal.json" + '?' + new Date().getTime(), (data) => {
//             let $select = $('#' + elementId);
//             $select.empty();
//             data.forEach((entry) => {
//                 let prefixName = (language === 'TH') ? entry.prefixTh : entry.prefixEn;
//                 $select.append($('<option></option>').attr('value', entry.id).text(prefixName));
//             });

//             if (data.length > 0) {
//                 $select.select2();

//                 if (elementKey) {
//                     $select.val(elementKey).trigger('change');
//                 }

//             } else {
//                 console.error("ไม่มีข้อมูลสำหรับสร้าง options");
//             }
//         });
//     }

//     function setLoadProvinces(language, elementId, elementKey, code = null) {

//             $.getJSON("../api/address/provinces.json" + '?' + new Date().getTime(), (data) => {
                
//                 let $select = $('#' + elementId);
//                 $select.empty();
    
//                 let filteredData = code ? data.filter(entry => parseInt(entry.provinceCode) === parseInt(code)) : data;
    
//                 filteredData.forEach((entry) => {
//                     let provinceName = (language === 'TH') ? entry.provinceNameTh : entry.provinceNameEn;
//                     $select.append($('<option></option>')
//                         .attr('value', entry.provinceCode)
//                         .text(provinceName));
//                 });
    
//                 if (filteredData.length > 0) {
//                     $select.select2({
//                         templateResult: setTemplateAddress,
//                         templateSelection: setTemplateAddress
//                     });
    
//                     if (elementKey) {
//                         $select.val(elementKey).trigger('change');
//                     }
    
//                 } else {
//                     console.error("ไม่มีข้อมูลสำหรับสร้าง options");
//                 }
//             });

//     }

//     function setLoadDistricts(language, elementId, elementKey, provCode = null, distCode = null) {

//             $.getJSON("../api/address/districts.json" + '?' + new Date().getTime(), (data) => {
//                 let $select = $('#' + elementId);
//                 $select.empty();
    
//                 let filteredData = provCode ? data.filter(entry => parseInt(entry.provinceCode) === parseInt(provCode)) : data;
//                 let filteredDist = distCode ? filteredData.filter(entry => parseInt(entry.districtCode) === parseInt(distCode)) : filteredData;
    
//                 filteredDist.forEach((entry) => {
//                     let districtName = (language === 'TH') ? entry.districtNameTh : entry.districtNameEn;
//                     let $option = $('<option></option>')
//                         .attr('value', entry.districtCode)
//                         .attr('data-prov', entry.provinceCode)
//                         .attr('data-dist', entry.districtCode)
//                         .attr('data-type', 'dt')
//                         .text(districtName);
//                     $select.append($option);
//                 });
    
//                 if (filteredDist.length > 0) {
//                     $select.select2({
//                         templateResult: setTemplateAddress,
//                         templateSelection: setTemplateAddress
//                     });
    
//                     if (elementKey) {
//                         $select.val(elementKey).trigger('change');
//                     }
    
//                 } else {
//                     console.error("ไม่มีข้อมูลสำหรับสร้าง options");
//                 }
//             });

//     }

//     function setLoadSubdistricts(language, elementId, elementKey, provCode = null, distCode = null) {

//             $.getJSON("../api/address/subdistricts.json" + '?' + new Date().getTime(), (data) => {
//                 let $select = $('#' + elementId);
//                 $select.empty();

//                 let filteredData = provCode ? data.filter(entry => parseInt(entry.provinceCode) === parseInt(provCode)) : data;
//                 let filteredSubd = distCode ? filteredData.filter(entry => parseInt(entry.districtCode) === parseInt(distCode)) : filteredData;
                
    
//                 filteredSubd.forEach((entry) => {
//                     let subdistrictName = (language === 'TH') ? entry.subdistrictNameTh : entry.subdistrictNameEn;
//                     $select.append($('<option></option>')
//                         .attr('value', entry.subdistrictCode)
//                         .attr('data-prov', entry.provinceCode)
//                         .attr('data-dist', entry.districtCode)
//                         .attr('data-type', 'sdt')
//                         .text(subdistrictName));
//                 });
    
//                 if (filteredSubd.length > 0) {
//                     $select.select2({
//                         templateResult: setTemplateAddress,
//                         templateSelection: setTemplateAddress
//                     });
    
//                     if (elementKey) {
//                         $select.val(elementKey).trigger('change');
//                     }
    
//                 } else {
//                     console.error("ไม่มีข้อมูลสำหรับสร้าง options");
//                 }
//             });

//     }

//     function setLoadPostalCodes(language, elementId, elementKey, provCode = null, distCode = null) {

//             $.getJSON("../api/address/districts.json" + '?' + new Date().getTime(), (data) => {
//                 let $select = $('#' + elementId);
//                 $select.empty();
//                 let filteredData = provCode ? data.filter(entry => parseInt(entry.provinceCode) === parseInt(provCode)) : data;
//                 let filteredSubd = distCode ? filteredData.filter(entry => parseInt(entry.districtCode) === parseInt(distCode)) : filteredData;
    
//                 filteredSubd.forEach((entry) => {
//                     $select.append($('<option></option>')
//                         .attr('value', entry.postalCode)
//                         .attr('data-prov', entry.provinceCode)
//                         .attr('data-dist', entry.districtCode)
//                         .text(entry.postalCode));
//                 });
    
//                 if (filteredSubd.length > 0) {

//                     $select.select2({
//                         templateResult: setTemplateAddress,
//                         templateSelection: setTemplateAddress
//                     });

//                     if (elementKey) {
//                         $select.val(elementKey).trigger('change');
//                     }
    
//                 } else {
//                     console.error("ไม่มีข้อมูลสำหรับสร้าง options");
//                 }
//             });

//     }

//     function setTemplateAddress(state) {
//         if (!state.id) {
//             return state.text;
//         }
    
//         var dataType = $(state.element).data('type') || '';
//         var html = `
//             <span>
//                 ${state.text}
//                 <button type="button" data-id="${dataType}" class="ml-auto cancel-address remove-btn">
//                     <i class="fas fa-times" style="font-size: 12px;"></i>
//                 </button>
//             </span>
//         `;
        
//         var $state = $(html);
    
//         return $state;
//     }
    

//     function setFormatState(state) {
//         if (!state.id) {
//             return state.text;
//         }

//         var flagUrl = $(state.element).data('flag');
//         var $state = $(
//             '<span><img src="' + flagUrl + '" class="img-flag" style="width:20px; margin-right: 10px;" /> ' + state.text + '</span>'
//         );
//         return $state;
//     }

//     setInit();
// }


function setFormAddress(language, elementId, elementName, elementKey) {
    let currentLang = language || 'US';
    let numberId = elementId.replace(/[^0-9]/g, '');

    // Object to cache loaded data
    const cachedData = {
        nations: null,
        prefixes: null,
        provinces: null,
        districts: null,
        subdistricts: null,
        postalCodes: null
    };

    function setInit() {
        loadInitialData();
        setBindEvents();
    }

    function loadInitialData() {
        switch (elementName) {
            case 'prefix':
                loadPrefix(currentLang, elementId, elementKey);
                break;
            case 'country':
                loadNation(currentLang, elementId, elementKey);
                break;
            case 'province':
                loadProvinces(currentLang, elementId, elementKey);
                break;
            case 'district':
                loadDistricts(currentLang, elementId, elementKey);
                break;
            case 'subdistrict':
                loadSubdistricts(currentLang, elementId, elementKey);
                break;
            case 'post_code':
                loadPostalCodes(currentLang, elementId, elementKey);
                break;
            default:
                console.warn("Unknown element name");
                break;
        }
    }

    function setBindEvents() {
        $('#language-select').on('change', updateLanguage);
        setChangeEventHandlers();
        $(document).on('click', '.cancel-address', handleCancelAddress);
    }

    function updateLanguage() {
        currentLang = $('#language-select').val() || 'US';
        loadInitialData();
    }

    function setChangeEventHandlers() {
        const elementTypeHandlers = {
            province: () => $(document).on('change', `#province_${numberId}`, handleProvinceChange),
            district: () => $(document).on('change', `#district_${numberId}`, handleDistrictChange),
            subdistrict: () => $(document).on('change', `#subdistrict_${numberId}`, handleSubdistrictChange),
            post_code: () => $(document).on('change', `#post_code_${numberId}`, handlePostalCodeChange)
        };

        if (elementTypeHandlers[elementName]) {
            elementTypeHandlers[elementName]();
        }
    }

    function handleProvinceChange(event) {
        const provinceCode = $(this).val();
        loadDistricts(currentLang, `district_${numberId}`, null, provinceCode);
        loadSubdistricts(currentLang, `subdistrict_${numberId}`, null, provinceCode);
        loadPostalCodes(currentLang, `post_code_${numberId}`, null, provinceCode);
    }

    function handleDistrictChange(event) {
        const provCode = $(this).find('option:selected').data('prov');
        const distCode = $(this).find('option:selected').data('dist');
        // loadProvinces(currentLang, `province_${numberId}`, null, provCode);
        // loadPostalCodes(currentLang, `post_code_${numberId}`, null, provCode, distCode);
        loadSubdistricts(currentLang, `subdistrict_${numberId}`, null, provCode, distCode);
    }

    function handleSubdistrictChange(event) {
        const provCode = $(this).find('option:selected').data('prov');
        const distCode = $(this).find('option:selected').data('dist');
        // loadProvinces(currentLang, `province_${numberId}`, null, provCode);
        // loadDistricts(currentLang, `district_${numberId}`, null, provCode, distCode);
        loadPostalCodes(currentLang, `post_code_${numberId}`, null, provCode, distCode);
    }

    function handlePostalCodeChange(event) {
        const provCode = $(this).find('option:selected').data('prov');
        const distCode = $(this).find('option:selected').data('dist');
        // loadProvinces(currentLang, `province_${numberId}`, null, provCode);
        // loadDistricts(currentLang, `district_${numberId}`, null, provCode, distCode);
    }

    function handleCancelAddress(event) {
        const dataID = $(event.target).data('id');
        resetFormSet(dataID);
    }

    function resetFormSet(dataID) {
        loadPrefix(currentLang, `prefix_${numberId}`, null);
        loadProvinces(currentLang, `province_${numberId}`, null);
        loadDistricts(currentLang, `district_${numberId}`, null);
        loadSubdistricts(currentLang, `subdistrict_${numberId}`, null);
        loadPostalCodes(currentLang, `post_code_${numberId}`, null);
    }

    function loadNation(language, elementId, elementKey) {
        if (cachedData.nations) {
            populateNationSelect(cachedData.nations, elementId);
        } else {
            $.getJSON("../api/languages/nation.json" + '?' + new Date().getTime(), function(data) {
                cachedData.nations = data.nationalities.filter(entry => entry.abbreviation === 'TH');
                populateNationSelect(cachedData.nations, elementId);
            });
        }
    }

    function populateNationSelect(nationalities, elementId) {
        const $select = $(`#${elementId}`);
        $select.empty();
        nationalities.forEach(entry => {
            const option = $('<option></option>')
                .attr('value', entry.abbreviation)
                .attr('data-flag', entry.flag)
                .text(entry.name);
            $select.append(option);
        });

        if (nationalities.length > 0) {
            $select.select2({
                templateResult: setFormatState,
                templateSelection: setFormatState
            });
        } else {
            console.error("ไม่มีข้อมูลสำหรับสร้าง options");
        }
    }

    function loadPrefix(language, elementId, elementKey) {
        if (cachedData.prefixes) {
            populatePrefixSelect(cachedData.prefixes, elementId, elementKey);
        } else {
            $.getJSON("../api/person/personal.json" + '?' + new Date().getTime(), (data) => {
                cachedData.prefixes = data;
                populatePrefixSelect(data, elementId, elementKey);
            });
        }
    }

    function populatePrefixSelect(data, elementId, elementKey) {
        const $select = $(`#${elementId}`);
        $select.empty();
        data.forEach(entry => {
            const prefixName = (currentLang === 'TH') ? entry.prefixTh : entry.prefixEn;
            $select.append($('<option></option>').attr('value', entry.id).text(prefixName));
        });

        if (data.length > 0) {
            $select.select2();
            if (elementKey) {
                $select.val(elementKey).trigger('change');
            }
        } else {
            console.error("ไม่มีข้อมูลสำหรับสร้าง options");
        }
    }

    function loadProvinces(language, elementId, elementKey, code = null) {
        if (cachedData.provinces) {
            populateProvincesSelect(cachedData.provinces, elementId, elementKey, code);
        } else {
            $.getJSON("../api/address/provinces.json" + '?' + new Date().getTime(), (data) => {
                cachedData.provinces = data;
                populateProvincesSelect(data, elementId, elementKey, code);
            });
        }
    }

    function populateProvincesSelect(data, elementId, elementKey, code = null) {
        const $select = $(`#${elementId}`);
        $select.empty();
    
        if (!elementKey) {
            $select.append($('<option></option>').attr('value', '').text('กรุณาเลือก'));
        } 
        
    
        const filteredData = code ? data.filter(entry => parseInt(entry.provinceCode) === parseInt(code)) : data;
    
        filteredData.forEach(entry => {
            const provinceName = (currentLang === 'TH') ? entry.provinceNameTh : entry.provinceNameEn;
            $select.append($('<option></option>')
                .attr('value', entry.provinceCode)
                .text(provinceName));
        });
    
        if (filteredData.length > 0) {
            $select.select2({
                templateResult: setTemplateAddress,
                templateSelection: setTemplateAddress
            });
            if (elementKey) {
                $select.val(elementKey).trigger('change');
            }
        } else {
            console.error("ไม่มีข้อมูลสำหรับสร้าง options");
        }
    }
    
    function loadDistricts(language, elementId, elementKey, provCode = null, distCode = null) {
        if (cachedData.districts) {
            populateDistrictsSelect(cachedData.districts, elementId, elementKey, provCode, distCode);
        } else {
            $.getJSON("../api/address/districts.json" + '?' + new Date().getTime(), (data) => {
                cachedData.districts = data;
                populateDistrictsSelect(data, elementId, elementKey, provCode, distCode);
            });
        }
    }

    function populateDistrictsSelect(data, elementId, elementKey, provCode = null, distCode = null) {
        const $select = $(`#${elementId}`);
        $select.empty();

        if (!provCode && !elementKey && !distCode) {
            $select.prop('disabled', true);
            $select.append($('<option></option>').attr('value', '').text('กรุณาเลือก'));
            return;
        } else {
            $select.prop('disabled', false);
        }

        const filteredData = provCode ? data.filter(entry => parseInt(entry.provinceCode) === parseInt(provCode)) : data;
        const filteredDist = distCode ? filteredData.filter(entry => parseInt(entry.districtCode) === parseInt(distCode)) : filteredData;

        filteredDist.forEach(entry => {
            const districtName = (currentLang === 'TH') ? entry.districtNameTh : entry.districtNameEn;
            $select.append($('<option></option>')
                .attr('value', entry.districtCode)
                .attr('data-prov', entry.provinceCode)
                .attr('data-dist', entry.districtCode)
                .attr('data-type', 'dt')
                .text(districtName));
        });

        if (filteredDist.length > 0) {
            $select.select2({
                templateResult: setTemplateAddress,
                templateSelection: setTemplateAddress
            });
            if (elementKey) {
                $select.val(elementKey).trigger('change');
            }
        } else {
            console.error("ไม่มีข้อมูลสำหรับสร้าง options");
        }
    }

    function loadSubdistricts(language, elementId, elementKey, provCode = null, distCode = null) {
        if (cachedData.subdistricts) {
            populateSubdistrictsSelect(cachedData.subdistricts, elementId, elementKey, provCode, distCode);
        } else {
            $.getJSON("../api/address/subdistricts.json" + '?' + new Date().getTime(), (data) => {
                cachedData.subdistricts = data;
                populateSubdistrictsSelect(data, elementId, elementKey, provCode, distCode);
            });
        }
    }

    function populateSubdistrictsSelect(data, elementId, elementKey, provCode = null, distCode = null) {
        const $select = $(`#${elementId}`);
        $select.empty();

        if (!provCode && !elementKey && !distCode) {
            $select.prop('disabled', true);
            $select.append($('<option></option>').attr('value', '').text('กรุณาเลือก'));
            return;
        } else {
            $select.prop('disabled', false);
        }

        const filteredData = provCode ? data.filter(entry => parseInt(entry.provinceCode) === parseInt(provCode)) : data;
        const filteredSubd = distCode ? filteredData.filter(entry => parseInt(entry.districtCode) === parseInt(distCode)) : filteredData;

        filteredSubd.forEach(entry => {
            const subdistrictName = (currentLang === 'TH') ? entry.subdistrictNameTh : entry.subdistrictNameEn;
            $select.append($('<option></option>')
                .attr('value', entry.subdistrictCode)
                .attr('data-prov', entry.provinceCode)
                .attr('data-dist', entry.districtCode)
                .attr('data-type', 'sdt')
                .text(subdistrictName));
        });

        if (filteredSubd.length > 0) {
            $select.select2({
                templateResult: setTemplateAddress,
                templateSelection: setTemplateAddress
            });
            if (elementKey) {
                $select.val(elementKey).trigger('change');
            }
        } else {
            console.error("ไม่มีข้อมูลสำหรับสร้าง options");
        }
    }

    function loadPostalCodes(language, elementId, elementKey, provCode = null, distCode = null) {
        if (cachedData.postalCodes) {
            populatePostalCodesSelect(cachedData.postalCodes, elementId, elementKey, provCode, distCode);
        } else {
            $.getJSON("../api/address/districts.json" + '?' + new Date().getTime(), (data) => {
                cachedData.postalCodes = data;
                populatePostalCodesSelect(data, elementId, elementKey, provCode, distCode);
            });
        }
    }

    function populatePostalCodesSelect(data, elementId, elementKey, provCode = null, distCode = null) {
        const $select = $(`#${elementId}`);
        $select.empty();

        if (!provCode && !elementKey && !distCode) {
            $select.prop('disabled', true);
            $select.append($('<option></option>').attr('value', '').text('กรุณาเลือก'));
            return;
        } else {
            $select.prop('disabled', false);
        }

        const filteredData = provCode ? data.filter(entry => parseInt(entry.provinceCode) === parseInt(provCode)) : data;
        const filteredSubd = distCode ? filteredData.filter(entry => parseInt(entry.districtCode) === parseInt(distCode)) : filteredData;

        filteredSubd.forEach(entry => {
            $select.append($('<option></option>')
                .attr('value', entry.postalCode)
                .attr('data-prov', entry.provinceCode)
                .attr('data-dist', entry.districtCode)
                .text(entry.postalCode));
        });

        if (filteredSubd.length > 0) {
            $select.select2({
                templateResult: setTemplateAddress,
                templateSelection: setTemplateAddress
            });
            if (elementKey) {
                $select.val(elementKey).trigger('change');
            }
        } else {
            console.error("ไม่มีข้อมูลสำหรับสร้าง options");
        }
    }

    function setTemplateAddress(state) {
        if (!state.id) return state.text;

        const dataType = $(state.element).data('type') || '';
        return $(`
            <span>
                ${state.text}
                <button type="button" data-id="${dataType}" class="ml-auto cancel-address remove-btn">
                    <i class="fas fa-times" style="font-size: 12px;"></i>
                </button>
            </span>
        `);
    }

    function setFormatState(state) {
        if (!state.id) return state.text;

        const flagUrl = $(state.element).data('flag');
        return $(`
            <span>
                <img src="${flagUrl}" class="img-flag" style="width:20px; margin-right: 10px;" /> ${state.text}
            </span>
        `);
    }

    setInit();
}



