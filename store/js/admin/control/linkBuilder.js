export const DynamicSectionManager = {
    // Configuration
    SECTION_LIMIT: 4,
    sectionCounter: 0,
    sections: new Map(),

    // DOM elements (will be initialized)
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
                        img.url || img.src,   // รองรับทั้ง url, src
                        element.dataset.id,
                        img.category || ""
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
                            category
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
                data.forEach(menu => DynamicSectionManager.addMenuItem(container, menu.label, menu.href, element.dataset.id));
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
                            element.dataset.id
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
                    element.querySelector(".text-content").value = data;
                }
            },
            bindEvents(element) {
                const textarea = element.querySelector(".text-content");
                textarea.setAttribute("name", `sections[${element.dataset.id}][text]`);
            }
        }
    },

    // ================= INITIALIZATION =================
    init(options = {}) {
        // Set configuration
        this.SECTION_LIMIT = options.sectionLimit || 4;
        
        // Get DOM elements
        this.typeSelector = document.getElementById(options.typeSelectorId || "link_type");
        this.addSectionBtn = document.getElementById(options.addButtonId || "add-section-btn");
        this.dynamicContainer = document.getElementById(options.containerId || "dynamic-content-container");
        this.limitMessage = document.getElementById(options.limitMessageId || "limit-message");

        // Bind main event
        if (this.addSectionBtn) {
            this.addSectionBtn.addEventListener("click", () => this.addSection());
        }

        return this; // For method chaining
    },

    // ================= SECTION MANAGEMENT =================
    createSection(type, data = null) {
        const sectionConfig = this.sectionTypes[type];
        if (!sectionConfig) {
            throw new Error(`Unknown section type: ${type}`);
        }

        const sectionId = `section-${this.sectionCounter++}`;
        
        // Create section element
        const section = document.createElement("div");
        section.className = "card-hyperlink-section mb-3 p-3 border rounded";
        section.dataset.type = type;
        section.dataset.id = sectionId;

        section.innerHTML = `
            <div class="d-flex justify-content-between mb-3">
                <h5>${sectionConfig.title}</h5>
                <button type="button" class="delete-section-btn btn btn-sm btn-outline-danger">
                    <i class="fa-solid fa-trash"></i> ลบ
                </button>
            </div>
            ${sectionConfig.template}
        `;

        // Add to container
        this.dynamicContainer.appendChild(section);

        // Bind delete button
        section.querySelector(".delete-section-btn").addEventListener("click", () => {
            this.removeSection(sectionId);
        });

        // Populate data if provided
        if (data) {
            sectionConfig.populate(section, data);
        }

        // Bind section-specific events
        sectionConfig.bindEvents(section);

        // Store section reference
        this.sections.set(sectionId, {
            element: section,
            type: type,
            id: sectionId
        });

        return { sectionId, element: section };
    },

    addSection(type = null, data = null) {
        // Check limit
        if (this.sections.size >= this.SECTION_LIMIT) {
            this.showLimitMessage();
            return null;
        }

        const sectionType = type || (this.typeSelector ? this.typeSelector.value : 'text');

        try {
            return this.createSection(sectionType, data);
        } catch (error) {
            console.error("Error creating section:", error);
            return null;
        }
    },

    removeSection(sectionId) {
        const sectionData = this.sections.get(sectionId);
        if (sectionData) {
            sectionData.element.remove();
            this.sections.delete(sectionId);
        }
    },

    removeAllSections() {
        this.sections.forEach((sectionData) => {
            sectionData.element.remove();
        });
        this.sections.clear();
    },

    // ================= ITEM MANAGEMENT =================
    addImageItem(container, url, sectionId, category = "") {
        const index = container.querySelectorAll(".image-url-input").length;
        const div = document.createElement("div");
        // div.className = "d-flex align-items-center mb-2";
        div.setAttribute("style", "display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 5px 0px;");
        div.innerHTML = `
            <img src="${url}" style="width:64px;height:64px;" class="rounded me-2">

            <input type="text" 
                name="sections[${sectionId}][images][${index}][url]" 
                value="${url}" 
                class="image-url-input form-input me-2">

            <select name="sections[${sectionId}][images][${index}][category]" 
                class="form-input me-2">
                <option value="">-- เลือกประเภท --</option>
                <option value="banner" ${category === "banner" ? "selected" : ""}>Banner</option>
                <option value="gallery" ${category === "gallery" ? "selected" : ""}>Gallery</option>
                <option value="thumbnail" ${category === "thumbnail" ? "selected" : ""}>Thumbnail</option>
            </select>

            <button type="button" class="delete-item-btn btn btn-sm btn-danger">ลบ</button>
        `;

        div.querySelector(".delete-item-btn").addEventListener("click", () => div.remove());
        container.appendChild(div);
    },

    addMenuItem(container, label, href, sectionId) {
        const index = container.querySelectorAll(".menu-label-input").length;
        const div = document.createElement("div");
        // div.className = "d-flex align-items-center mb-2";
        div.setAttribute("style", "display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 5px 0px;");
        div.innerHTML = `
            <input type="text" 
                name="sections[${sectionId}][menus][${index}][label]" 
                value="${label}" 
                class="menu-label-input form-input me-2">
            <input type="text" 
                name="sections[${sectionId}][menus][${index}][href]" 
                value="${href}" 
                class="menu-href-input form-input me-2">
            <button type="button" class="delete-item-btn btn btn-sm btn-danger"><i class="bi bi-x"></i></button>
        `;
        div.querySelector(".delete-item-btn").addEventListener("click", () => div.remove());
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

    getSectionById(sectionId) {
        return this.sections.get(sectionId);
    },

    getSectionCount() {
        return this.sections.size;
    },

    // ================= EXTENSION METHODS =================
    addSectionType(typeName, config) {
        if (!config.title || !config.template || !config.populate || !config.bindEvents) {
            throw new Error("Section type config must include: title, template, populate, bindEvents");
        }
        this.sectionTypes[typeName] = config;
    },

    removeSectionType(typeName) {
        delete this.sectionTypes[typeName];
    },

    // ================= HELPER METHODS =================
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
