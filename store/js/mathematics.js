
const MathTools = {
  state: {
    expanded: false,
    contents: []
  },

  init() {
    this.injectStyle();
    this.createButton();
    this.createBox();
    this.bindEvents();
  },

  injectStyle() {
    const style = document.createElement("style");
    style.textContent = `
      #math-button {
        position: fixed;
        bottom: 20px;
        left: 20px;
        background: linear-gradient(135deg, #f18b20, #ffb347);
        color: white;
        border: none;
        border-radius: 50px;
        padding: 14px 28px;
        cursor: pointer;
        box-shadow: 0 6px 12px rgba(241, 139, 32, 0.6);
        font-weight: 600;
        font-size: 16px;
        transition: background 0.3s ease, box-shadow 0.3s ease;
        z-index: 1000;
      }
      #math-button:hover {
        background: linear-gradient(135deg, #ffb347, #f18b20);
        box-shadow: 0 8px 20px rgba(241, 139, 32, 0.8);
      }

      #math-box {
        display: none;
        position: fixed;
        bottom: 160px;
        right: 20px;
        width: 360px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.2);
        z-index: 1000;
        user-select: none;
        overflow: hidden;
        transition: transform 0.3s ease, opacity 0.3s ease;
      }

      #math-box.show {
        display: block;
        opacity: 1;
        transform: translateY(0);
      }

      #math-header {
        background: linear-gradient(135deg, #f18b20, #ffb347);
        color: white;
        padding: 14px 20px;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
        font-weight: 700;
        font-size: 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 3px 8px rgba(0,0,0,0.15);
      }

      #math-header button {
        background: transparent;
        border: none;
        color: white;
        font-size: 20px;
        font-weight: 700;
        cursor: pointer;
        padding: 0;
        line-height: 1;
        transition: color 0.2s ease;
      }
      #math-header button:hover {
        color: #222;
      }

      #math-tabs {
        display: flex;
        border-bottom: 2px solid #eee;
        background: #fafafa;
      }

      .math-tab {
        flex: 1;
        padding: 14px 0;
        text-align: center;
        cursor: pointer;
        font-weight: 600;
        color: #555;
        transition: background 0.3s ease, color 0.3s ease;
        user-select: none;
        border-bottom: 3px solid transparent;
      }
      .math-tab:hover {
        background: #fff3e0;
        color: #f18b20;
      }
      .math-tab.active {
        background: white;
        color: #f18b20;
        border-bottom: 3px solid #f18b20;
        font-weight: 700;
      }

      .math-content {
        display: none;
        padding: 20px;
        height: 320px;
        overflow-y: auto;
        background: white;
        font-size: 16px;
        color: #333;
        transition: opacity 0.3s ease;
      }
      .math-content.active {
        display: block;
      }

      .math-form {
        display: flex !important;
        flex-direction: column;
        gap: 10px;
      }

      .math-input {
        width: 100%;
        padding: 0.5rem 3rem 0.3rem 2.5rem;
        border: 1px solid #ccc;
        border-radius: 1.5rem;
        font-size: 0.85rem;
        box-sizing: border-box;
      }
      .math-input:focus {
        border-color: #ff9800;
        box-shadow: 0 0 11px 3px #ff980030;
        outline: none;
      }

      .math-select {
        padding: 6px 10px;
        font-size: 16px;
        border-radius: 4px;
        margin-bottom: 10px;
        border: 1px solid #eaeaea;
      }

      .math-content button {
        background: #f18b20;
        color: white;
        border: none;
        padding: 0.2rem 3rem 0.2rem 3rem;
        font-size: 14px;
        border-radius: 30px;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(241, 139, 32, 0.6);
        transition: background 0.3s ease, box-shadow 0.3s ease;
        user-select: none;
      }
      .math-content button:hover {
        background: #ffb347;
        box-shadow: 0 6px 18px rgba(241, 139, 32, 0.8);
      }

      .math-content div {
        font-size: 14px;
        color: #444;
        min-height: 30px;
      }

      /* Responsive */
      @media screen and (max-width: 900px) {
        #math-box {
          width: 90vw;
          bottom: 80px;
          right: 10px;
          max-height: 60vh;
          border-radius: 10px;
        }
      }

      mark {
        background: limegreen;
        color: white;
        padding: 0 2px;
        border-radius: 3px;
      }
    `;
    document.head.appendChild(style);
  },

  createButton() {
    this.button = document.createElement("button");
    this.button.id = "math-button";
    this.button.innerText = "Math Tools";
    document.body.appendChild(this.button);
  },

  createBox() {
    this.box = document.createElement("div");
    this.box.id = "math-box";

    // Header
    this.header = document.createElement("div");
    this.header.id = "math-header";

    const title = document.createElement("span");
    title.innerText = "Math Tools";

    const right = document.createElement("div");
    right.style.display = "flex";
    right.style.gap = "5px";

    this.expandBtn = this.makeButton("⬜", "expand");
    this.closeBtn = this.makeButton("X", "close");

    right.appendChild(this.expandBtn);
    right.appendChild(this.closeBtn);

    this.header.appendChild(title);
    this.header.appendChild(right);
    this.box.appendChild(this.header);

    // Tabs
    this.tabContainer = document.createElement("div");
    this.tabContainer.id = "math-tabs";
    this.box.appendChild(this.tabContainer);

    this.createTabs();

    document.body.appendChild(this.box);
  },

  makeButton(text, action) {
    const btn = document.createElement("button");
    btn.innerText = text;
    btn.dataset.action = action;
    btn.style.background = "transparent";
    btn.style.border = "none";
    btn.style.color = "white";
    btn.style.fontSize = action === "expand" ? "18px" : "16px";
    btn.style.cursor = "pointer";
    return btn;
  },

  createTabs() {
    const tabsData = [
      { key: "math-lotto1", label: "เลขลับ", handler: this.tabLotto1.bind(this) },
      { key: "math-lotto2", label: "เลขรวม", handler: this.tabLotto2.bind(this) },
      { key: "math-lotto3", label: "เปรียบเทียบ", handler: this.tabLotto3.bind(this) },
    ];

    this.state.tabs = [];

    tabsData.forEach((tab, i) => {
      // ปุ่ม Tab
      const tabBtn = document.createElement("div");
      tabBtn.className = "math-tab" + (i === 0 ? " active" : "");
      tabBtn.dataset.tab = tab.key;
      tabBtn.innerText = tab.label;
      this.tabContainer.appendChild(tabBtn);

      // พื้นที่แสดงผล
      const content = document.createElement("div");
      content.id = tab.key;
      content.className = "math-content" + (i === 0 ? " active" : "");

      const result = document.createElement("div");
      result.className = "math-result";
      content.appendChild(result);

      this.box.appendChild(content);
      this.state.contents.push(content);

      // เก็บไว้ใช้ใน bindEvents
      this.state.tabs.push({ tabBtn, content, handler: tab.handler, result });
    });
  },

  bindEvents() {
    this.button.addEventListener("click", () => {
      this.box.style.display = "block";
      this.button.style.display = "none";

      // เปิดกล่องแล้วให้ tab แรกทำงานทันที
      const firstTab = this.state.tabs[0];
      if (firstTab) firstTab.handler(null, firstTab.result);
    });

    this.closeBtn.addEventListener("click", () => {
      this.box.style.display = "none";
      this.button.style.display = "block";
    });

    this.expandBtn.addEventListener("click", () => this.toggleExpand());

    // คลิก Tab แล้วสลับ + เรียก handler
    this.state.tabs.forEach(({ tabBtn, content, handler, result }) => {
      tabBtn.addEventListener("click", () => {
        this.tabContainer.querySelectorAll(".math-tab").forEach(t => t.classList.remove("active"));
        this.state.contents.forEach(c => c.classList.remove("active"));

        tabBtn.classList.add("active");
        content.classList.add("active");

        // เคลียร์ผลลัพธ์เก่า
        result.innerHTML = "";
        // เรียก handler ของ tab
        handler(null, result);
      });
    });
  },

  toggleExpand() {
    if (!this.state.expanded) {
      this.box.style.width = "95vw";
      this.box.style.height = "85vh";
      // this.box.style.right = "2.5vw";
      this.box.style.bottom = "4.5vh";

      this.state.contents.forEach(c => {
        c.style.height = "calc(100% - 120px)";
      });

      this.expandBtn.innerText = "🗕";
    } else {
      this.box.style.width = "360px";
      this.box.style.height = "auto";
      this.box.style.bottom = "160px";
      this.box.style.right = "20px";

      this.state.contents.forEach(c => {
        c.style.height = "320px";
      });

      this.expandBtn.innerText = "⬜";
    }
    this.state.expanded = !this.state.expanded;
  },

  // ===== Handlers ของแต่ละ TAB =====
  tabLotto1(form, result) {
    result.innerHTML = `
      <div>
        <input type="text" id="lotto1-input" class="math-input" placeholder="กรอกเลขอย่างน้อย 8 หลัก" />
        <button id="lotto1-btn" class="math-btn mt-2">คำนวณ</button>
        <div id="lotto1-output"></div>
      </div>
    `;

    const input = result.querySelector("#lotto1-input");
    const btn = result.querySelector("#lotto1-btn");
    const output = result.querySelector("#lotto1-output");

    btn.addEventListener("click", () => {
      const num = input.value.trim();
      const arr = Array.from(num, Number);

      if (num.length < 8 || arr.some(isNaN)) {
        output.innerHTML = "<br>กรุณากรอกเลขอย่างน้อย 8 หลัก (ตัวเลขเท่านั้น)";
        return;
      }

      const sum456 = arr[3] + arr[4] + arr[5];
      const sum678 = arr[5] + arr[6] + arr[7];
      const last456 = sum456 % 10;
      const last678 = sum678 % 10;

      const suggest456 = [0, 5, 6, 7, 8].map((n) => (last456 + n) % 10);
      const suggest678 = [0, 5, 6, 7, 8].map((n) => (last678 + n) % 10);

      const duplicates = suggest456.filter((n) => suggest678.includes(n));

      function highlightVertical(arr) {
        return arr.map((n) => {
          const content = duplicates.includes(n) ? `<mark>${n}</mark>` : n;
          return `<div>${content}</div>`;
        }).join("");
      }

      function highlightPairs(pairSet) {
        return Array.from(pairSet).map((pair) => {
          const [a, b] = pair.split("").map(Number);
          const markedA = duplicates.includes(a) ? `<mark>${a}</mark>` : a;
          const markedB = duplicates.includes(b) ? `<mark>${b}</mark>` : b;
          return `<div>${markedA}${markedB} = ${a + b}</div>`;
        }).join("");
      }

      // === Pair logic ===
      const crossPairs = new Set();
      const self456Pairs = new Set();
      const self678Pairs = new Set();

      function addPair(set, a, b) {
        if (a !== b) {
          const pair = `${a}${b}`;
          const reversePair = `${b}${a}`;
          if (!set.has(pair) && !set.has(reversePair)) set.add(pair);
        }
      }

      suggest456.forEach((a) => suggest678.forEach((b) => addPair(crossPairs, a, b)));
      for (let i = 0; i < suggest456.length; i++) {
        for (let j = i + 1; j < suggest456.length; j++) addPair(self456Pairs, suggest456[i], suggest456[j]);
      }
      for (let i = 0; i < suggest678.length; i++) {
        for (let j = i + 1; j < suggest678.length; j++) addPair(self678Pairs, suggest678[i], suggest678[j]);
      }

      const crossPairsHtml = highlightPairs(crossPairs);
      const self456PairsHtml = highlightPairs(self456Pairs);
      const self678PairsHtml = highlightPairs(self678Pairs);

      output.innerHTML = `
        <br>
        <div>เลขที่กรอก: ${num}</div>
        <div>ถอดรหัส: ${sum456} → ${last456} | ${sum678} → ${last678}</div>
        <div style="
        display:grid;
        grid-template-columns:repeat(auto-fit, minmax(100px,1fr));
        gap:16px;
        align-items:start;
        margin-top:8px;
        ">
          <div style="padding:8px; border:1px solid #eee; border-radius:8px;">
            <b>เข้ารหัสกลุ่ม 1:</b><br>${highlightVertical(suggest456)}
          </div>
          <div style="padding:8px; border:1px solid #eee; border-radius:8px;">
            <b>เข้ารหัสกลุ่ม 2:</b><br>${highlightVertical(suggest678)}
          </div>
          <div style="padding:8px; border:1px solid #eee; border-radius:8px;">
            <b>กลุ่มฝั่งซ้าย:</b><br>${self456PairsHtml}
          </div>
          <div style="padding:8px; border:1px solid #eee; border-radius:8px;">
            <b>กลุ่มฝั่งขวา:</b><br>${self678PairsHtml}
          </div>
          <div style="padding:8px; border:1px solid #eee; border-radius:8px;">
            <b>ข้ามกลุ่ม:</b><br>${crossPairsHtml}
          </div>
        </div>
      `;
    });
  },

  tabLotto2(form, result) {
    result.innerHTML = `
      <div>
        <select id="lotto2-select" class="math-select"></select><br>
        <button id="lotto2-btn" class="math-btn">หาคู่เลขที่รวมได้</button>
        <div id="lotto2-output"></div>
      </div>
    `;

    const select = result.querySelector("#lotto2-select");
    const btn = result.querySelector("#lotto2-btn");
    const output = result.querySelector("#lotto2-output");

    for (let i = 0; i <= 18; i++) {
      const opt = document.createElement("option");
      opt.value = i;
      opt.innerText = `ผลรวม: ${i}`;
      select.appendChild(opt);
    }

    btn.addEventListener("click", () => {
      const selectedSum = parseInt(select.value);
      const results = [];

      for (let a = 0; a <= 9; a++) {
        for (let b = a; b <= 9; b++) {
          if (a + b === selectedSum) {
            results.push(`(${a}, ${b})`);
          }
        }
      }

      output.innerHTML = `
        <br>
        <div>ผลรวมที่เลือก: <b>${selectedSum}</b></div>
        <div>คู่เลขที่เป็นไปได้ (${results.length} ชุด):</div>
        <div>${results.join(", ")}</div>
      `;
    });
  },

  tabLotto3(form, result) {
    result.innerHTML = `
      <div>
        <textarea id="lotto3-input" class="math-input" rows="2" placeholder="กรอกเลขหลายชุด เช่น 26, 38 56,45"></textarea>
        <button id="lotto3-btn" class="math-btn mt-2">วิเคราะห์</button>
        <div id="lotto3-output"></div>
      </div>
    `;

    const input = result.querySelector("#lotto3-input");
    const btn = result.querySelector("#lotto3-btn");
    const output = result.querySelector("#lotto3-output");

    btn.addEventListener("click", () => {
      const raw = input.value.trim();
      if (!raw) {
        output.innerHTML = "<br>กรุณากรอกเลขอย่างน้อย 1 ชุด";
        return;
      }

      const groups = raw.split(/[\s,]+/);
      const validGroups = groups.filter((group) => /^\d+$/.test(group));
      const invalidGroups = groups.filter((group) => !/^\d+$/.test(group));

      const results = validGroups.map((group) => {
        const digits = Array.from(group, Number);
        const sum = digits.reduce((a, b) => a + b, 0);
        return { group, sum, detail: `${group} → ${digits.join(" + ")} = ${sum}` };
      });

      const sums = results.map((r) => r.sum);

      const frequency = results.reduce((acc, current) => {
        acc[current.sum] = (acc[current.sum] || 0) + 1;
        return acc;
      }, {});

      const digitFrequency = {};
      results.forEach(({ group }) => {
        for (const digit of group) {
          digitFrequency[digit] = (digitFrequency[digit] || 0) + 1;
        }
      });

      const resultHtml = results.map((r) => `<div>${r.detail}</div>`).join("");
      const digitFrequencyHtml = Object.entries(digitFrequency)
        .sort((a, b) => a[0] - b[0])
        .map(([digit, count]) => `<div>เลข ${digit}: ${count} ครั้ง</div>`)
        .join("");
      const percentageHtml = Object.entries(frequency)
        .map(([sum, count]) => {
          const percentage = ((count / validGroups.length) * 100).toFixed(2);
          return `<div>ผลรวม ${sum}: ปรากฏ ${count} ครั้ง (${percentage}%)</div>`;
        }).join("");
      const diffHtml = sums.slice(1).map((sum, i) => {
        const prevSum = sums[i];
        const diff = sum - prevSum;
        return `<div>ผลต่างระหว่างชุดที่ ${i + 2} กับชุดที่ ${i + 1} = ${diff}</div>`;
      }).join("");
      const invalidHtml = invalidGroups.length ? `<div style="color:red;"><br>ไม่ถูกต้อง: ${invalidGroups.join(", ")}</div>` : "";

      output.innerHTML = `
      <div style="
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
      gap: 16px;
      align-items: start;
      margin-top: 8px;
      ">
        <div style="padding:8px; border:1px solid #eee; border-radius:8px;">${resultHtml}</div>
        <div style="padding:8px; border:1px solid #eee; border-radius:8px;">${digitFrequencyHtml}</div>
        <div style="padding:8px; border:1px solid #eee; border-radius:8px;">${percentageHtml}</div>
        <div style="padding:8px; border:1px solid #eee; border-radius:8px;">${diffHtml}${invalidHtml}</div>
      </div>
      `;
    });
  }

};

MathTools.init();
