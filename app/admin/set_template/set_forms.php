<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customizable Layout with Selectable Row Deletion</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            height: 100vh;
            margin: 0;
        }
    
        .controls {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 10px;
            background-color: #f0f0f0;
            flex: 0 0 200px; /* Width of the controls bar */
            border-right: 2px solid #ddd;
        }
        .dropzone {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 10px;
            flex-grow: 1; /* Take up remaining space */
            overflow-y: auto;
        }
        .row {
            display: grid;
            gap: 10px;
            width: 100%;
            cursor: pointer; /* Change cursor to indicate selectable rows */
        }
        .target-cell {
            border: 1px solid #ddd;
            min-height: 40px;
            background-color: #e0f7fa;
            position: relative;
        }
        .selected {
            background-color: #ffcc80;
            box-shadow: 1px 2px 4px #ffcc80;
        }
        .draggable {
            padding: 8px;
            border: 1px solid #ccc;
            background-color: #fff;
            cursor: grab;
        }
        /* .delete-button {
            background-color: #ff4444;
            color: white;
            border: none;
            padding: 5px;
            cursor: pointer;
        } */

        .input-class {
            background-color: #f0f8ff; /* สีพื้นหลัง */
            border: 1px solid #ccc; /* ขอบ */
            padding: 10px; /* ช่องว่างภายใน */
            margin: 5px; /* ช่องว่างภายนอก */
        }

        .button-class {
            background-color: #4CAF50; /* สีพื้นหลัง */
            color: white; /* สีข้อความ */
            border: none; /* ไม่แสดงขอบ */
            padding: 10px 15px; /* ช่องว่าง */
            cursor: pointer; /* เปลี่ยนเคอร์เซอร์ */
        }

        .select-class {
            background-color: #ffffff; /* สีพื้นหลัง */
            border: 1px solid #ccc; /* ขอบ */
            padding: 5px; /* ช่องว่าง */
            margin: 5px; /* ช่องว่าง */
        }

        .textarea-class {
            background-color: #f9f9f9; /* สีพื้นหลัง */
            border: 1px solid #ccc; /* ขอบ */
            padding: 10px; /* ช่องว่าง */
        }

        .checkbox-class,
        .radio-class {
            margin-right: 5px; /* ช่องว่างระหว่างป้ายกำกับ */
        }

        .file-class {
            background-color: #ffffff; /* สีพื้นหลัง */
            border: 1px solid #ccc; /* ขอบ */
            padding: 5px; /* ช่องว่าง */
        }

        .color-class {
            padding: 5px; /* ช่องว่าง */
            border: 1px solid #ccc; /* ขอบ */
        }


    </style>
</head>
<body>


<div class="controls">
    <label>Row column configuration: 
        <input type="text" id="rowConfigInput" placeholder="e.g., 3,2,4">
    </label>

    <button id="addRowButton">Add Row</button>
    <button id="deleteSelectedRowsButton">Delete Selected Rows</button>
    <button id="saveLayoutButton">Save Layout</button>
    <button id="resetLayoutButton">Reset Layout</button>

    <label>Update Selected Row (set columns): 
        <input type="number" id="selectedRowConfigInput" placeholder="e.g., 2" min="1">
        <button id="updateSelectedRowButton">Update Selected Row</button>
    </label>

    <div class="downloadContent"></div>

    <label for="classSelector">Select CSS Class: 
        <select id="classSelector">
            <option value="">None</option>
            <option value="input-class">Input Field</option>
            <option value="button-class">Button</option>
            <option value="select-class">Select Dropdown</option>
            <option value="textarea-class">Textarea</option>
            <option value="checkbox-class">Checkbox</option>
            <option value="radio-class">Radio Button</option>
            <option value="file-class">File Upload</option>
            <option value="color-class">Color Picker</option>
        </select>
    </label>
    <button id="applyClassButton">Apply Class</button>
</div>

<div class="controls">
    <div class="draggable" draggable="true" data-element="input">Input Field</div>
    <div class="draggable" draggable="true" data-element="button">Button</div>
    <div class="draggable" draggable="true" data-element="select">Select Dropdown</div>
    <div class="draggable" draggable="true" data-element="textarea">Textarea</div>
    <div class="draggable" draggable="true" data-element="checkbox">Checkbox</div>
    <div class="draggable" draggable="true" data-element="radio">Radio Button</div>
    <div class="draggable" draggable="true" data-element="file">File Upload</div>
    <div class="draggable" draggable="true" data-element="color">Color Picker</div>
</div>


<div id="targetZone" class="dropzone"></div>



<script>
    const targetZone = document.getElementById('targetZone');
    const rowConfigInput = document.getElementById('rowConfigInput');
    const addRowButton = document.getElementById('addRowButton');
    const deleteSelectedRowsButton = document.getElementById('deleteSelectedRowsButton');
    const saveLayoutButton = document.getElementById('saveLayoutButton');
    const resetLayoutButton = document.getElementById('resetLayoutButton');

    const selectedRowConfigInput = document.getElementById('selectedRowConfigInput');
    const updateSelectedRowButton = document.getElementById('updateSelectedRowButton');
    const classSelector = document.getElementById('classSelector');
    const applyClassButton = document.getElementById('applyClassButton');

    function createCustomGrid(rowConfig) {
        targetZone.innerHTML = '';
        rowConfig.forEach(columns => addRow(columns));
    }

    function addRow(columns) {
        const row = document.createElement('div');
        row.classList.add('row');
        row.style.gridTemplateColumns = `repeat(${columns}, 1fr)`;

        for (let i = 0; i < columns; i++) {
            const cell = document.createElement('div');
            cell.classList.add('target-cell');
            cell.setAttribute('data-cell', `${i}`);
            cell.addEventListener('dragover', (e) => e.preventDefault());
            cell.addEventListener('drop', handleDrop);
            row.appendChild(cell);
        }

        row.addEventListener('click', () => {
            row.classList.toggle('selected');
        });

        targetZone.appendChild(row);
    }

    document.querySelectorAll('.draggable').forEach(item => {
        item.addEventListener('dragstart', handleDragStart);
    });

    function handleDragStart(e) {
        e.dataTransfer.setData('text/plain', e.target.getAttribute('data-element'));
    }

    function handleDrop(e) {
        const elementType = e.dataTransfer.getData('text/plain');
        let element;

        switch (elementType) {
            case 'input':
                element = document.createElement('input');
                element.type = 'text';
                element.placeholder = 'Input Field';
                break;
            case 'button':
                element = document.createElement('button');
                element.textContent = 'Button';
                break;
            case 'select':
                element = document.createElement('select');
                const option1 = document.createElement('option');
                option1.text = 'Option 1';
                const option2 = document.createElement('option');
                option2.text = 'Option 2';
                element.add(option1);
                element.add(option2);
                break;
            case 'textarea':
                element = document.createElement('textarea');
                element.placeholder = 'Textarea';
                break;
            case 'checkbox':
                element = document.createElement('input');
                element.type = 'checkbox';
                element.id = 'checkbox' + Date.now();
                const label = document.createElement('label');
                label.setAttribute('for', element.id);
                label.textContent = 'Checkbox';
                element.appendChild(label);
                break;
            case 'radio':
                element = document.createElement('input');
                element.type = 'radio';
                element.name = 'radioGroup'; 
                element.id = 'radio' + Date.now();
                const radioLabel = document.createElement('label');
                radioLabel.setAttribute('for', element.id);
                radioLabel.textContent = 'Radio Button';
                element.appendChild(radioLabel);
                break;
            case 'file':
                element = document.createElement('input');
                element.type = 'file';
                break;
            case 'color':
                element = document.createElement('input');
                element.type = 'color';
                break;
        }

        if (e.target.classList.contains('target-cell') && !e.target.hasChildNodes()) {
            element.classList.add('draggable');

            element.addEventListener('dblclick', () => {
                e.target.removeChild(element);
            });

            e.target.appendChild(element);
        }

    }

    applyClassButton.addEventListener('click', () => {
        let currentClass = classSelector.value;

        
        const inputFields = targetZone.querySelectorAll(
            '#targetZone > div.row.selected > div > input, ' +
            '#targetZone > div.row.selected > div > button, ' +
            '#targetZone > div.row.selected > div > select, ' +
            '#targetZone > div.row.selected > div > textarea, ' +
            '#targetZone > div.row.selected > div > [type="checkbox"], ' +
            '#targetZone > div.row.selected > div > [type="radio"], ' +
            '#targetZone > div.row.selected > div > input[type="file"], ' +
            '#targetZone > div.row.selected > div > input[type="color"]'
        );

        // Loop through each input field and apply the class
        inputFields.forEach(inputField => {
            if (currentClass) {
                inputField.className = '';
                inputField.classList.add(currentClass); 
            }
        });
    });

    addRowButton.addEventListener('click', () => {
        const columns = parseInt(rowConfigInput.value);
        if (columns > 0) {
            addRow(columns);
        }
    });

    deleteSelectedRowsButton.addEventListener('click', () => {
        const rows = targetZone.querySelectorAll('.row.selected');
        rows.forEach(row => {
            targetZone.removeChild(row);
        });
    });

    updateSelectedRowButton.addEventListener('click', () => {
        const selectedRows = targetZone.querySelectorAll('.row.selected');
        const newColumnCount = parseInt(selectedRowConfigInput.value);

        selectedRows.forEach(row => {
            row.style.gridTemplateColumns = `repeat(${newColumnCount}, 1fr)`;

            while (row.firstChild) {
                row.removeChild(row.firstChild);
            }

            for (let i = 0; i < newColumnCount; i++) {
                const newCell = document.createElement('div');
                newCell.classList.add('target-cell');
                newCell.setAttribute('data-cell', `${i}`);
                newCell.addEventListener('dragover', (e) => e.preventDefault());
                newCell.addEventListener('drop', handleDrop);
                row.appendChild(newCell);
            }
        });
    });

    resetLayoutButton.addEventListener('click', () => {
        targetZone.innerHTML = '';
        
        const contentContainer = document.querySelector('.downloadContent');
        contentContainer.innerHTML = '';
    });

    saveLayoutButton.addEventListener('click', () => {
        const rows = targetZone.querySelectorAll('.row');
        const layout = Array.from(rows).map(row => {
            return {
                columns: row.style.gridTemplateColumns,
                cells: Array.from(row.children).map(cell => cell.innerHTML)
            };
        });

        const contentContainer = document.querySelector('.downloadContent');

        let htmlContent = '<div>\n'; // สตริงสำหรับเก็บ HTML

        layout.forEach(item => {
            htmlContent += `<div style="display: grid; grid-template-columns: ${item.columns}; gap: 10px;">\n`;

            item.cells.forEach(cell => {
                htmlContent += `  <div>${cell}</div>\n`; 
            });

            htmlContent += '</div>\n';
        });

        htmlContent += '</div>\n';


        // console.log('htmlContent', htmlContent);
        
        
        const downloadButton = document.createElement('button');
        downloadButton.innerText = 'Download HTML';
        downloadButton.type = 'button'; 

        downloadButton.addEventListener('click', () => {
            const blob = new Blob([htmlContent], { type: 'text/html' });
            const url = URL.createObjectURL(blob);
            
            const a = document.createElement('a');
            a.href = url;
            a.download = 'layout.html'; 
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a); 
            URL.revokeObjectURL(url);
        });

        contentContainer.appendChild(downloadButton);
    });

</script>

</body>
</html>
