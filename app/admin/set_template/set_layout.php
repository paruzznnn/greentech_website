<?php
include('../../../lib/permissions.php');
include('../../../lib/base_directory.php');
// checkPermissions();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel='icon' type='image/x-icon' href='../../../public/img/logo-ALLABLE-07.ico'>

    <?php include 'inc_head.php'?>
    <link href="../css/index_.css?v=<?php echo time();?>" rel="stylesheet">

    <style>
        /* body {
            font-family: Arial, sans-serif;
            display: flex;
            height: 100vh;
            margin: 0;
        } */
    
        .controls {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 10px;
            background-color: #f0f0f0;
            /* flex: 0 0 200px;  */
            border-right: 2px solid #ddd;
        }


        .dropzone {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 15px;
            flex-grow: 1;
            overflow-y: auto;
            background-color: #fafafa;
            border: 2px dashed #ccc;
            border-radius: 8px;
        }

        .target-row {
            display: grid;
            gap: 10px;
            width: 100%;
            cursor: pointer; 
        }

        .target-cell {
            border: 1px solid #ddd;
            min-height: 50px;
            background-color: #e0f7fa;
            border-radius: 4px;
            padding: 8px; 
            position: relative;
            transition: background-color 0.3s ease;
        }

        .target-cell:hover {
            background-color: #b2ebf2;
        }

        .selected {
            background-color: #ffcc80;
            box-shadow: 1px 2px 4px rgba(255, 204, 128, 0.6);
            border-color: #ffa726;
        }

        .draggable {
            padding: 10px;
            border: 1px solid #ccc;
            background-color: #fff;
            cursor: grab;
            border-radius: 4px;
            transition: background-color 0.2s ease;
        }

        .draggable:active {
            background-color: #ececec;
            cursor: grabbing;
        }

        .input-class {
            background-color: #f0f8ff;
            border: 1px solid #ccc;
            padding: 10px;
            margin: 5px;
        }

        .button-class {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
        }

        .select-class {
            background-color: #ffffff;
            border: 1px solid #ccc;
            padding: 5px;
            margin: 5px;
        }

        .textarea-class {
            background-color: #f9f9f9;
            border: 1px solid #ccc;
            padding: 10px;
        }

        .checkbox-class,
        .radio-class {
            margin-right: 5px;
        }

        .file-class {
            background-color: #ffffff;
            border: 1px solid #ccc;
            padding: 5px;
        }

        .color-class {
            padding: 5px;
            border: 1px solid #ccc;
        }


    </style>
</head>
<body>

<?php include '../template/header.php'?>

<div class="content-sticky" id="">
    <div class="container-fluid">
        <div class="box-content">
            <div class="row">

                <div class="col-md-3">

                    <div class="controls">
                        <label>Row column configuration: 
                            <input type="text" id="rowConfigInput" class="form-control" placeholder="e.g., 3,2,4">
                        </label>

                        <button id="addRowButton" type="button" class="btn btn-primary">Add Row</button>
                        <button id="deleteSelectedRowsButton" type="button" class="btn btn-danger">Delete Selected Rows</button>
                        <button id="resetLayoutButton" type="button" class="btn btn-warning">Reset Layout</button>

                        <label>Update Selected Row (set columns): 
                            <input type="number" id="selectedRowConfigInput" class="form-control" placeholder="e.g., 2" min="1">
                            <button id="updateSelectedRowButton" type="button" class="btn btn-warning">Update Selected Row</button>
                        </label>

                        <label for="classSelector">Select CSS Class: 
                            <select id="classSelector" class="form-select">
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
                        <button id="applyClassButton" type="button" class="btn btn-primary">Apply Class</button>
                    </div>
                
                </div>
                <div class="col-md-3">

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

                </div>
                <div class="col-md-6">

                    <div class="row">

                        <div class="col-md-12 d-flex justify-content-between mb-3 flex-wrap">
                            <div class="flex-fill mx-2">
                                <label for="mainDirectory">Main Directory:</label>
                                <select id="mainDirectory" class="form-select">
                                    <option value=""></option>
                                    <option value="set_template">Developer</option>
                                    <option value="set_news">News</option>
                                </select>
                            </div>

                            <div class="flex-fill mx-2">
                                <label for="subDirectory">Sub Directory:</label>
                                <select id="subDirectory" class="form-select">
                                    <option value=""></option>
                                </select>
                            </div>

                            <div class="flex-fill mx-2">
                                <label for="fileSystem">File:</label>
                                <input type="text" id="fileSystem" class="form-control" placeholder="file name">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div id="targetZone" class="dropzone"></div>
                        </div>

                        <div class="col-md-12 mt-3">
                            <div style="float: inline-end;">
                                <button id="saveLayoutButton" type="button" class="btn btn-success ml-auto">
                                    Save Layout
                                </button>
                            </div>
                        </div>


                    </div>
                </div>

            </div>

        </div>
    </div>
</div>


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

    const mainDirectorySelector = document.getElementById('mainDirectory');
    const subDirectorySelector = document.getElementById('subDirectory');

    const fileSystemInput = document.getElementById('fileSystem');

    

    function createCustomGrid(rowConfig) {
        targetZone.innerHTML = '';
        rowConfig.forEach(columns => addRow(columns));
    }

    function addRow(columns) {
        const row = document.createElement('div');
        row.classList.add('target-row');
        row.style.gridTemplateColumns = `repeat(${columns}, 1fr)`;

        for (let i = 0; i < columns; i++) {
            const cell = document.createElement('div');
            cell.classList.add('target-cell');
            cell.setAttribute('data-cell', `${i}`);
            cell.addEventListener('dragover', (e) => e.preventDefault());
            cell.addEventListener('drop', handleDrop);

            cell.addEventListener('click', () => {
                cell.classList.toggle('selected');
            });

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
                break;
            case 'radio':
                element = document.createElement('input');
                element.type = 'radio';
                element.name = 'radioGroup'; 
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

    addRowButton.addEventListener('click', () => {
        const columns = parseInt(rowConfigInput.value);
        if (columns > 0) {
            addRow(columns);
        }
    });

    deleteSelectedRowsButton.addEventListener('click', () => {
        const rows = targetZone.querySelectorAll('.target-row.selected');
        rows.forEach(row => {
            targetZone.removeChild(row);
        });
    });

    updateSelectedRowButton.addEventListener('click', () => {
        const selectedRows = targetZone.querySelectorAll('.target-row.selected');
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

                // New event listener for click to alert
                newCell.addEventListener('click', () => {
                    newCell.classList.toggle('selected');
                });

                row.appendChild(newCell);
            }
        });
    });

    applyClassButton.addEventListener('click', () => {
        let currentClass = classSelector.value;

        const checkFields1 = targetZone.querySelectorAll(
            '#targetZone > div.target-row.selected > div.target-cell.selected > :is(input, button, select, textarea, [type="checkbox"], [type="radio"], input[type="file"], input[type="color"])'
        );

        const checkFields2 = targetZone.querySelectorAll(
            '#targetZone > div.target-row.selected > div.target-cell > :is(input, button, select, textarea, [type="checkbox"], [type="radio"], input[type="file"], input[type="color"])'
        );

        const checkFields3 = targetZone.querySelectorAll(
            '#targetZone > div.target-row > div.target-cell.selected > :is(input, button, select, textarea, [type="checkbox"], [type="radio"], input[type="file"], input[type="color"])'
        );

        // แปลง NodeList เป็น Array และรวมผลลัพธ์จากทั้งสอง NodeList
        const inputFields = [...checkFields1, ...checkFields2, ...checkFields3];

        // Loop through each input field and apply the class
        inputFields.forEach(inputField => {
            if (currentClass) {
                inputField.className = '';
                inputField.classList.add(currentClass); 
            }
        });
    });

    resetLayoutButton.addEventListener('click', () => {
        targetZone.innerHTML = '';
    });

    function buildHtml(layout){

        let htmlLayout = `
            <!DOCTYPE html>
            <html lang="th">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title></title>
                </head>
                <body>
        `;

        htmlLayout += `
        <div class="content-sticky" id="">
            <div class="container-fluid">
                <div class="box-content">
                    <div class="row">
        \n`;

        htmlLayout += '<div>\n';

        layout.forEach(item => {
            htmlLayout += `<div style="display: grid; grid-template-columns: ${item.columns}; gap: 10px;">\n`;
            
            item.cells.forEach(cell => {
                htmlLayout += `  <div style="margin: 10px;">${cell}</div>\n`; 
            });

            htmlLayout += '</div>\n';
        });

        htmlLayout += '</div>\n';

        htmlLayout += `
                    </div>
                </div>
            </div>
        </div>
        \n`;

        htmlLayout += `
            </body>
            </html>
        `;

        return htmlLayout;

    }

    // saveLayoutButton.addEventListener('click', () => {
    //     const rows = targetZone.querySelectorAll('.target-row');
    //     const layout = Array.from(rows).map(row => {
    //         return {
    //             columns: row.style.gridTemplateColumns,
    //             cells: Array.from(row.children).map(cell => cell.innerHTML)
    //         };
    //     });

    //     const contentContainer = document.querySelector('.downloadContent');

    //     let htmlContent = buildHtml(layout);

    //     const downloadButton = document.createElement('button');
    //     downloadButton.innerText = 'Download HTML';
    //     downloadButton.type = 'button'; 

    //     downloadButton.addEventListener('click', () => {
    //         const blob = new Blob([htmlContent], { type: 'text/html' });
    //         const url = URL.createObjectURL(blob);
            
    //         const a = document.createElement('a');
    //         a.href = url;
    //         a.download = 'layout.html'; 
    //         document.body.appendChild(a);
    //         a.click();
    //         document.body.removeChild(a); 
    //         URL.revokeObjectURL(url);
    //     });

    //     contentContainer.appendChild(downloadButton);
    // });

    saveLayoutButton.addEventListener('click', () => {
        const rows = targetZone.querySelectorAll('.target-row');
        const layout = Array.from(rows).map(row => {
            return {
                columns: row.style.gridTemplateColumns,
                cells: Array.from(row.children).map(cell => cell.innerHTML)
            };
        });

        let htmlContent = buildHtml(layout);
        let mainFolder = mainDirectorySelector.value;
        let subFolder = subDirectorySelector.value;
        let fileName = fileSystemInput.value;

        $.ajax({
            url: 'actions/save_layout.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(
                { 
                    htmlContent,
                    mainFolder, 
                    subFolder,
                    fileName
                }
            ),
            success: function(data) {
                console.log('Data:', data);  
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    });

</script>

<script src="../js/index_.js?v=<?php echo time();?>"></script>
</body>
</html>
