export async function fetchLinkData(req, call) {
    try {
        const params = new URLSearchParams({
            action: req.action,
            id: req.id
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

export const DynamicSectionManager = {
    // Configuration
    SECTION_LIMIT: 4,
    sectionCounter: 0,
    sections: new Map(),

    // DOM elements
    typeSelector: null,
    addSectionBtn: null,
    dynamicContainer: null,
    limitMessage: null,

    // ================= SECTION TYPES =================
    sectionTypes: {
        image: {
            title: "ส่วนรูปภาพ",
            template: `
                <div class="image-container"></div>
                <div class="mt-3" style="display: flex; gap: 15px;">
                    <input type="text" class="new-image-url form-input" placeholder="กรอก URL รูปภาพ...">
                    <select class="new-image-category form-input" style="max-width:140px;">
                        <option value="">-- เลือกประเภท --</option>
                        <option value="banner">Banner</option>
                        <option value="gallery">Gallery</option>
                        <option value="thumbnail">Thumbnail</option>
                    </select>
                    <button type="button" class="add-image-btn btn btn-primary">เพิ่ม</button>
                </div>
            `,
            populate(element, data) {
                if (!data) return;
                const container = element.querySelector(".image-container");
                data.forEach(img => {
                    DynamicSectionManager.addImageItem(
                        container,
                        img.url || img.src,
                        element.dataset.id,
                        img.category || "",
                        true,               // isFromDB
                        img.id || 0,        // id
                        img.status || "existing"
                    );
                });
            },
            bindEvents(element) {
                const addBtn = element.querySelector(".add-image-btn");
                const urlInput = element.querySelector(".new-image-url");
                const catInput = element.querySelector(".new-image-category");

                addBtn.addEventListener("click", () => {
                    const url = urlInput.value.trim();
                    const category = catInput.value.trim();
                    if (url) {
                        DynamicSectionManager.addImageItem(
                            element.querySelector(".image-container"),
                            url,
                            element.dataset.id,
                            category,
                            false, // new
                            0,
                            "new"
                        );
                        urlInput.value = "";
                        catInput.value = "";
                    }
                });
            }
        },

        menu: {
            title: "ส่วนเมนู",
            template: `
                <div class="menu-container"></div>
                <div class="mt-3">
                    <input type="text" class="new-menu-label form-input mb-2" placeholder="ชื่อเมนู">
                    <input type="text" class="new-menu-href form-input mb-2" placeholder="URL">
                    <button type="button" class="add-menu-btn btn btn-primary w-100">เพิ่มเมนู</button>
                </div>
            `,
            populate(element, data) {
                if (!data) return;
                const container = element.querySelector(".menu-container");
                data.forEach(menu => DynamicSectionManager.addMenuItem(
                    container,
                    menu.label,
                    menu.href,
                    element.dataset.id,
                    true,
                    menu.id || 0,
                    menu.status || "existing"
                ));
            },
            bindEvents(element) {
                const addBtn = element.querySelector(".add-menu-btn");
                const labelInput = element.querySelector(".new-menu-label");
                const hrefInput = element.querySelector(".new-menu-href");

                addBtn.addEventListener("click", () => {
                    const label = labelInput.value.trim();
                    const href = hrefInput.value.trim();
                    if (label && href) {
                        DynamicSectionManager.addMenuItem(
                            element.querySelector(".menu-container"),
                            label,
                            href,
                            element.dataset.id,
                            false,
                            0,
                            "new"
                        );
                        labelInput.value = "";
                        hrefInput.value = "";
                    }
                });
            }
        },

        text: {
            title: "ส่วนข้อความ",
            template: `
                <textarea class="text-content form-input" rows="4" placeholder="กรอกข้อความรายละเอียด..."></textarea>
            `,
            populate(element, data) {
                if (data) {
                    element.querySelector(".text-content").value = data.text || "";
                    // เพิ่ม hidden id
                    const hiddenId = document.createElement("input");
                    hiddenId.type = "hidden";
                    hiddenId.name = `sections[${element.dataset.id}][id]`;
                    hiddenId.value = data.id || 0;
                    element.appendChild(hiddenId);
                }
            },
            bindEvents(element) {
                const textarea = element.querySelector(".text-content");
                textarea.setAttribute("name", `sections[${element.dataset.id}][text]`);

                // === NEW: mark updated when editing existing text ===
                textarea.addEventListener("input", () => {
                    const sectionStatusInput = element.querySelector(".section-status");
                    if (sectionStatusInput.value === "existing") {
                        sectionStatusInput.value = "updated";
                    }
                });
            }
        }
    },

    // ================= INITIALIZATION =================
    init(options = {}) {
        this.SECTION_LIMIT = options.sectionLimit || 4;

        this.typeSelector = document.getElementById(options.typeSelectorId || "link_type");
        this.addSectionBtn = document.getElementById(options.addButtonId || "add-section-btn");
        this.dynamicContainer = document.getElementById(options.containerId || "dynamic-content-container");
        this.limitMessage = document.getElementById(options.limitMessageId || "limit-message");

        if (this.addSectionBtn) {
            this.addSectionBtn.addEventListener("click", () => this.addSection());
        }
        return this;
    },

    // ================= SECTION MANAGEMENT =================
    createSection(type, data = null, isFromDB = false) {
        const sectionConfig = this.sectionTypes[type];
        if (!sectionConfig) throw new Error(`Unknown section type: ${type}`);

        const sectionId = `section-${this.sectionCounter++}`;
        const section = document.createElement("div");
        section.className = "card-hyperlink-section mb-3 p-3 border rounded";
        section.dataset.type = type;
        section.dataset.id = sectionId;

        const sectionDbId = Array.isArray(data) ? 0 : (data?.id || 0);

        section.innerHTML = `
            <div class="d-flex justify-content-between mb-3">
                <h5>${sectionConfig.title}</h5>
                <button type="button" class="delete-section-btn btn btn-sm btn-outline-danger">
                    <i class="fa-solid fa-trash"></i> ลบ
                </button>
            </div>
            ${sectionConfig.template}
            <input type="hidden" class="section-status" name="sections[${sectionId}][status]" value="${isFromDB ? "existing" : "new"}">
            <input type="hidden" class="section-id" name="sections[${sectionId}][id]" value="${sectionDbId}">
        `;

        this.dynamicContainer.appendChild(section);

        section.querySelector(".delete-section-btn").addEventListener("click", () => {
            this.markSectionDeleted(sectionId);
        });

        if (data) sectionConfig.populate(section, data);
        sectionConfig.bindEvents(section);

        this.sections.set(sectionId, {
            element: section,
            type: type,
            id: sectionId,
            status: isFromDB ? "existing" : "new"
        });

        return { sectionId, element: section };
    },

    addSection(type = null, data = null, isFromDB = false) {
        if (this.sections.size >= this.SECTION_LIMIT) {
            this.showLimitMessage();
            return null;
        }
        const sectionType = type || (this.typeSelector ? this.typeSelector.value : "text");
        try {
            return this.createSection(sectionType, data, isFromDB);
        } catch (error) {
            console.error("Error creating section:", error);
            return null;
        }
    },

    markSectionDeleted(sectionId) {
        const sectionData = this.sections.get(sectionId);
        if (sectionData) {
            sectionData.status = "deleted";
            sectionData.element.style.display = "none";

            const sectionStatusInput = sectionData.element.querySelector(".section-status");
            if (sectionStatusInput) sectionStatusInput.value = "deleted";

            sectionData.element.querySelectorAll(".item-status").forEach(input => {
                input.value = "deleted";
                const parent = input.closest(".image-item, .menu-item");
                if (parent) {
                    parent.dataset.status = "deleted";
                    parent.style.display = "none";
                }
            });

            const textInput = sectionData.element.querySelector(".text-content");
            if (textInput) {
                let textStatus = sectionData.element.querySelector(".text-status");
                if (!textStatus) {
                    textStatus = document.createElement("input");
                    textStatus.type = "hidden";
                    textStatus.className = "text-status";
                    textStatus.name = `sections[${sectionId}][text_status]`;
                    sectionData.element.appendChild(textStatus);
                }
                textStatus.value = "deleted";
            }
        }
    },

    removeAllSections() {
        this.sections.forEach((sectionData) => {
            sectionData.element.remove();
        });
        this.sections.clear();
    },

    // ================= ITEM MANAGEMENT =================
    addImageItem(container, url, sectionId, category = "", isFromDB = false, id = 0, status = null) {
        const index = container.querySelectorAll(".image-item").length;
        const div = document.createElement("div");
        const finalStatus = status || (isFromDB ? "existing" : "new");

        div.classList.add("image-item");
        div.dataset.status = finalStatus;

        div.setAttribute("style", "display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 5px 0px;");
        div.innerHTML = `
            <img src="${url}" style="width:64px;height:64px;" class="preview-img rounded me-2">
            <input type="hidden" name="sections[${sectionId}][images][${index}][id]" value="${id}">
            <input type="text" name="sections[${sectionId}][images][${index}][url]" value="${url}" class="image-url-input form-input me-2">
            <select name="sections[${sectionId}][images][${index}][category]" class="form-input me-2">
                <option value="">-- เลือกประเภท --</option>
                <option value="banner" ${category === "banner" ? "selected" : ""}>Banner</option>
                <option value="gallery" ${category === "gallery" ? "selected" : ""}>Gallery</option>
                <option value="thumbnail" ${category === "thumbnail" ? "selected" : ""}>Thumbnail</option>
            </select>
            <input type="hidden" class="item-status" name="sections[${sectionId}][images][${index}][status]" value="${finalStatus}">
            <button type="button" class="delete-item-btn btn btn-sm btn-danger"><i class="bi bi-x"></i></button>
        `;

        const urlInput = div.querySelector(".image-url-input");
        const previewImg = div.querySelector(".preview-img");

        // === preview update real-time ===
        urlInput.addEventListener("input", () => {
            previewImg.src = urlInput.value || "";
            markUpdated();
        });

        // === mark updated on change ===
        const markUpdated = () => {
            const statusInput = div.querySelector(".item-status");
            if (statusInput.value === "existing") {
                statusInput.value = "updated";
                div.dataset.status = "updated";
            }
        };
        div.querySelector("select").addEventListener("change", markUpdated);

        div.querySelector(".delete-item-btn").addEventListener("click", () => {
            div.dataset.status = "deleted";
            div.querySelector(".item-status").value = "deleted";
            div.style.display = "none";
        });

        container.appendChild(div);
    },

    addMenuItem(container, label, href, sectionId, isFromDB = false, id = 0, status = null) {
        const index = container.querySelectorAll(".menu-item").length;
        const div = document.createElement("div");
        const finalStatus = status || (isFromDB ? "existing" : "new");

        div.classList.add("menu-item");
        div.dataset.status = finalStatus;

        div.setAttribute("style", "display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 5px 0px;");
        div.innerHTML = `
            <input type="hidden" name="sections[${sectionId}][menus][${index}][id]" value="${id}">
            <input type="text" name="sections[${sectionId}][menus][${index}][label]" value="${label}" class="menu-label-input form-input me-2">
            <input type="text" name="sections[${sectionId}][menus][${index}][href]" value="${href}" class="menu-href-input form-input me-2">
            <input type="hidden" class="item-status" name="sections[${sectionId}][menus][${index}][status]" value="${finalStatus}">
            <button type="button" class="delete-item-btn btn btn-sm btn-danger"><i class="bi bi-x"></i></button>
        `;

        // === mark updated on input change ===
        const markUpdated = () => {
            const statusInput = div.querySelector(".item-status");
            if (statusInput.value === "existing") {
                statusInput.value = "updated";
                div.dataset.status = "updated";
            }
        };
        div.querySelector(".menu-label-input").addEventListener("input", markUpdated);
        div.querySelector(".menu-href-input").addEventListener("input", markUpdated);

        div.querySelector(".delete-item-btn").addEventListener("click", () => {
            div.dataset.status = "deleted";
            div.querySelector(".item-status").value = "deleted";
            div.style.display = "none";
        });

        container.appendChild(div);
    },

    // ================= UTILITY METHODS =================
    showLimitMessage() {
        if (this.limitMessage) {
            this.limitMessage.style.display = "block";
            setTimeout(() => {
                this.limitMessage.style.display = "none";
            }, 3000);
        }
    },

    getSections() {
        return Array.from(this.sections.values());
    },

    getFormattedData() {
        const result = [];
        this.sections.forEach(section => {
            const sec = {
                id: section.element.querySelector(".section-id")?.value || 0,
                type: section.type,
                status: section.element.querySelector(".section-status")?.value || section.status,
                data: {}
            };

            if (section.type === "image") {
                sec.data.images = Array.from(section.element.querySelectorAll(".image-item")).map(div => ({
                    id: div.querySelector("input[name*='[id]']").value,
                    url: div.querySelector(".image-url-input").value,
                    category: div.querySelector("select").value,
                    status: div.querySelector(".item-status").value
                }));
            }

            if (section.type === "menu") {
                sec.data.menus = Array.from(section.element.querySelectorAll(".menu-item")).map(div => ({
                    id: div.querySelector("input[name*='[id]']").value,
                    label: div.querySelector(".menu-label-input").value,
                    href: div.querySelector(".menu-href-input").value,
                    status: div.querySelector(".item-status").value
                }));
            }

            if (section.type === "text") {
                sec.data.text = section.element.querySelector(".text-content").value;
                sec.data.id = section.element.querySelector("input[name*='[id]']")?.value || 0;
                const textStatus = section.element.querySelector(".text-status");
                if (textStatus) {
                    sec.data.text_status = textStatus.value;
                }
            }

            result.push(sec);
        });
        return result;
    },

    getSectionById(sectionId) {
        return this.sections.get(sectionId);
    },

    getSectionCount() {
        return this.sections.size;
    },

    addSectionType(typeName, config) {
        if (!config.title || !config.template || !config.populate || !config.bindEvents) {
            throw new Error("Section type config must include: title, template, populate, bindEvents");
        }
        this.sectionTypes[typeName] = config;
    },

    removeSectionType(typeName) {
        delete this.sectionTypes[typeName];
    },

    reset() {
        this.removeAllSections();
        this.sectionCounter = 0;
    },

    getConfig() {
        return {
            sectionLimit: this.SECTION_LIMIT,
            sectionCount: this.getSectionCount(),
            availableTypes: Object.keys(this.sectionTypes)
        };
    }
};
