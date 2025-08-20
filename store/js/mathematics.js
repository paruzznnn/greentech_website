// ===== สร้าง CSS =====
const mathStyle = document.createElement("style");
mathStyle.textContent = `
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
  /* padding: 0.6rem 3.5rem 0.6rem 2.5rem; */
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

  `;
document.head.appendChild(mathStyle);

// ===== ปุ่มหลัก =====
const mathButton = document.createElement("button");
mathButton.id = "math-button";
mathButton.innerText = "Math Tools";
document.body.appendChild(mathButton);

// ===== กล่องหลัก =====
const mathBox = document.createElement("div");
mathBox.id = "math-box";

// Header
const mathHeader = document.createElement("div");
mathHeader.id = "math-header";

const mathTitle = document.createElement("span");
mathTitle.innerText = "Math Tools";

const mathCloseBtn = document.createElement("button");
mathCloseBtn.innerText = "X";
mathCloseBtn.style.background = "transparent";
mathCloseBtn.style.border = "none";
mathCloseBtn.style.color = "white";
mathCloseBtn.style.fontSize = "16px";
mathCloseBtn.style.cursor = "pointer";

mathHeader.appendChild(mathTitle);
mathHeader.appendChild(mathCloseBtn);
mathBox.appendChild(mathHeader);

// Tabs
const mathTabContainer = document.createElement("div");
mathTabContainer.id = "math-tabs";

const mathTabsData = [
  { key: "math-lotto1", label: "เลขลับ" },
  { key: "math-lotto2", label: "เลขรวม" },
  { key: "math-lotto3", label: "เปรียบเทียบ" },
];

const mathContents = [];

mathTabsData.forEach((tab, index) => {
  const mathTabBtn = document.createElement("div");
  mathTabBtn.className = "math-tab" + (index === 0 ? " active" : "");
  mathTabBtn.dataset.tab = tab.key;
  mathTabBtn.innerText = tab.label;
  mathTabContainer.appendChild(mathTabBtn);

  const mathContent = document.createElement("div");
  mathContent.id = tab.key;
  mathContent.className = "math-content" + (index === 0 ? " active" : "");

  const mathForm = document.createElement("form");
  mathForm.className = "math-form";

  const mathBtn = document.createElement("button");
  mathBtn.type = "submit";
  mathBtn.className = "math-submit";
  mathBtn.innerText = "วิเคราะห์";

  const mathResult = document.createElement("div");
  mathResult.className = "math-result";

  if (tab.key === "math-lotto1") {
    const input = document.createElement("input");
    input.type = "text";
    input.className = "math-input";
    input.placeholder = "กรอกเลข";
    input.id = `input-${tab.key}`;
    mathForm.appendChild(input);

    mathForm.addEventListener("submit", (event) => {
      event.preventDefault();
      const num = input.value.trim();
      const arr = Array.from(num, Number);

      if (num.length < 8) {
        mathResult.innerHTML = "<br>กรุณากรอกเลขอย่างน้อย 8 หลัก";
        return;
      }

      const sum456 = arr[3] + arr[4] + arr[5];
      const sum678 = arr[5] + arr[6] + arr[7];
      const last456 = sum456 % 10;
      const last678 = sum678 % 10;

      const suggest456 = [0, 5, 6, 7, 8].map((n) => (last456 + n) % 10);
      const suggest678 = [0, 5, 6, 7, 8].map((n) => (last678 + n) % 10);

      const duplicates = suggest456.filter((n) => suggest678.includes(n));

      const crossPairs = new Set();
      const self456Pairs = new Set();
      const self678Pairs = new Set();

      function addPair(set, a, b) {
        if (a !== b) {
          const pair = `${a}${b}`;
          const reversePair = `${b}${a}`;
          if (!set.has(pair) && !set.has(reversePair)) {
            set.add(pair);
          }
        }
      }

      suggest456.forEach((a) => {
        suggest678.forEach((b) => addPair(crossPairs, a, b));
      });

      for (let i = 0; i < suggest456.length; i++) {
        for (let j = i + 1; j < suggest456.length; j++) {
          addPair(self456Pairs, suggest456[i], suggest456[j]);
        }
      }

      for (let i = 0; i < suggest678.length; i++) {
        for (let j = i + 1; j < suggest678.length; j++) {
          addPair(self678Pairs, suggest678[i], suggest678[j]);
        }
      }

      function highlightVertical(arr) {
        return arr
          .map((n) => {
            const content = duplicates.includes(n) ? `<mark>${n}</mark>` : n;
            return `<div>${content}</div>`;
          })
          .join("");
      }

      const pairDigitFrequency = {};

      function highlightPairs(pairSet, freqCounter) {
        return Array.from(pairSet)
          .map((pair) => {
            const [a, b] = pair.split("").map(Number);

            freqCounter[a] = (freqCounter[a] || 0) + 1;
            freqCounter[b] = (freqCounter[b] || 0) + 1;

            const markedA = duplicates.includes(a) ? `<mark>${a}</mark>` : a;
            const markedB = duplicates.includes(b) ? `<mark>${b}</mark>` : b;
            const sum = a + b;

            return `<div>${markedA}${markedB} = ${sum}</div>`;
          })
          .join("");
      }

      const crossPairsHtml = highlightPairs(crossPairs, pairDigitFrequency);
      const self456PairsHtml = highlightPairs(self456Pairs, pairDigitFrequency);
      const self678PairsHtml = highlightPairs(self678Pairs, pairDigitFrequency);

      mathResult.innerHTML = `
      <br>
      <div>เลขที่กรอก: ${num}</div>
      <div>ถอดรหัส: ${sum456} → ${last456} | ${sum678} → ${last678}</div>
      <div style="display: flex; gap: 50px;">
        <div>
          <b>เข้ารหัสกลุ่ม 1:</b><br>${highlightVertical(suggest456)}
        </div>
        <div>
          <b>เข้ารหัสกลุ่ม 2:</b><br>${highlightVertical(suggest678)}
        </div>
      </div>
      <p><b>ค้นพบเลข:</b></p>
      <div><b>ข้ามกลุ่ม:</b><br>${crossPairsHtml}</div>
      <div><b>กลุ่มฝั่งซ้าย:</b><br>${self456PairsHtml}</div>
      <div><b>กลุ่มฝั่งขวา:</b><br>${self678PairsHtml}</div>
    `;
    });
  }

  if (tab.key === "math-lotto2") {
    const selectSum = document.createElement("select");
    selectSum.className = "math-select";
    selectSum.id = "select-lotto2";

    for (let i = 0; i <= 18; i++) {
      const opt = document.createElement("option");
      opt.value = i;
      opt.innerText = `ผลรวม: ${i}`;
      selectSum.appendChild(opt);
    }

    mathForm.appendChild(selectSum);
    mathBtn.innerText = "หาคู่เลขที่รวมได้";

    mathForm.addEventListener("submit", (event) => {
      event.preventDefault();
      const selectedSum = parseInt(selectSum.value);
      const results = [];

      for (let a = 0; a <= 9; a++) {
        for (let b = a; b <= 9; b++) {
          if (a + b === selectedSum) {
            results.push(`(${a}, ${b})`);
          }
        }
      }

      mathResult.innerHTML = `
        <br>
        <div>ผลรวมที่เลือก: <b>${selectedSum}</b></div>
        <div>คู่เลขที่เป็นไปได้ (${results.length} ชุด):</div>
        <div>${results.join(", ")}</div>
      `;
    });
  }

  if (tab.key === "math-lotto3") {
    const input = document.createElement("textarea");
    input.className = "math-input";
    input.placeholder = "กรอกเลขหลายชุด เช่น 26, 38 56,45";
    input.rows = 2;
    input.id = `input-${tab.key}`;
    mathForm.appendChild(input);

    mathForm.addEventListener("submit", (event) => {
      event.preventDefault();

      const raw = input.value.trim();
      if (!raw) {
        mathResult.innerHTML = "<br>กรุณากรอกเลขอย่างน้อย 1 ชุด";
        return;
      }

      const groups = raw.split(/[\s,]+/);
      const validGroups = groups.filter((group) => /^\d+$/.test(group));
      const invalidGroups = groups.filter((group) => !/^\d+$/.test(group));

      const results = validGroups.map((group) => {
        const digits = Array.from(group, Number);
        const sum = digits.reduce((a, b) => a + b, 0);
        return {
          group,
          sum,
          detail: `${group} → ${digits.join(" + ")} = ${sum}`,
        };
      });

      const sums = results.map((r) => r.sum);

      // คำนวณความถี่ของผลรวม
      const frequency = results.reduce((acc, current) => {
        acc[current.sum] = (acc[current.sum] || 0) + 1;
        return acc;
      }, {});

      // คำนวณความถี่ของตัวเลขแต่ละตัว
      const digitFrequency = {};
      results.forEach(({ group }) => {
        for (const digit of group) {
          digitFrequency[digit] = (digitFrequency[digit] || 0) + 1;
        }
      });

      // HTML แสดงผลรวมของแต่ละกลุ่ม
      const resultHtml = `
      <div><b>ผลรวม:</b></div>
      ${results.map((r) => `<div>${r.detail}</div>`).join("")}
    `;

      // HTML แสดงความถี่ของตัวเลขแต่ละตัว
      const digitFrequencyHtml = `
      <br>
      <div><b>ความถี่ของตัวเลขแต่ละตัว:</b></div>
      ${Object.entries(digitFrequency)
        .sort((a, b) => a[0] - b[0])
        .map(([digit, count]) => `<div>เลข ${digit}: ${count} ครั้ง</div>`)
        .join("")}
      <br>
    `;

      // HTML แสดงเปอร์เซ็นต์ของผลรวม
      const totalValidGroups = validGroups.length;
      const percentageHtml = `
      <br>
      <div><b>ความถี่และเปอร์เซ็นต์ของผลรวม:</b></div>
      ${Object.entries(frequency)
        .map(([sum, count]) => {
          const percentage = ((count / totalValidGroups) * 100).toFixed(2);
          return `<div>ผลรวม ${sum}: ปรากฏ ${count} ครั้ง (${percentage}%)</div>`;
        })
        .join("")}
      <br>
    `;

      // HTML แสดงผลต่างของผลรวม
      const diffHtml = sums
        .slice(1)
        .map((sum, i) => {
          const prevSum = sums[i];
          const diff = sum - prevSum;
          return `<div>ผลต่างระหว่างชุดที่ ${i + 2} กับชุดที่ ${
            i + 1
          } = ${diff}</div>`;
        })
        .join("");

      // HTML แสดงเลขไม่ถูกต้อง
      const invalidHtml = invalidGroups.length
        ? `<div style="color:red;"><br>รายการไม่ถูกต้อง: ${invalidGroups.join(
            ", "
          )}</div>`
        : "";

      // รวมผลลัพธ์ทั้งหมด
      mathResult.innerHTML = `<br>${resultHtml}${digitFrequencyHtml}${percentageHtml}${diffHtml}${invalidHtml}`;
    });
  }

  mathForm.appendChild(mathBtn);
  mathContent.appendChild(mathForm);
  mathContent.appendChild(mathResult);
  mathBox.appendChild(mathContent);
  mathContents.push(mathContent);
});

mathBox.insertBefore(mathTabContainer, mathBox.children[1]);
document.body.appendChild(mathBox);

// ===== Event =====
mathButton.addEventListener("click", () => {
  mathBox.style.display = "block";
  mathButton.style.display = "none";
});

mathCloseBtn.addEventListener("click", () => {
  mathBox.style.display = "none";
  mathButton.style.display = "block";
});

const mathTabs = mathTabContainer.querySelectorAll(".math-tab");
mathTabs.forEach((tab) => {
  tab.addEventListener("click", () => {
    mathTabs.forEach((t) => t.classList.remove("active"));
    mathContents.forEach((c) => c.classList.remove("active"));

    tab.classList.add("active");
    document.getElementById(tab.dataset.tab).classList.add("active");
  });
});
