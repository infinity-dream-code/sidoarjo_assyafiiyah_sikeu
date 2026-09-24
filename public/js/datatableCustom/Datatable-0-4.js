function debounce(func, delay) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), delay);
    };
}

function reformatNumber(data, row, column, node) {
    // replace spaces with nothing; replace commas with points.
    if (column === 1) {
        return data.replace(',', '.').replaceAll(' ', '');
    } else {
        return data;
    }
}

// function addCustomNumberFormat(xlsx, numberFormat) {
//     let numFmtsElement = xlsx.xl['styles.xml'].getElementsByTagName('numFmts')[0];
//     let numFmtElement = '<numFmt numFmtId="176" formatCode="' + numberFormat + '"/>';
//     $( numFmtsElement ).append( numFmtElement );
//     $( numFmtsElement ).attr("count", "7");
//
//     let celXfsElement = xlsx.xl['styles.xml'].getElementsByTagName('cellXfs');
//     let cellStyle = '<xf numFmtId="176" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"'
//         + ' applyFont="1" applyFill="1" applyBorder="1"/>';
//     $( celXfsElement ).append( cellStyle );
//     $( celXfsElement ).attr("count", "69");
// }

function ensureNumFmts(stylesXml) {
    let numFmts = stylesXml.getElementsByTagName('numFmts')[0];
    if (numFmts) return numFmts;

    numFmts = stylesXml.createElement('numFmts');
    numFmts.setAttribute('count', '0');

    const styleSheet = stylesXml.getElementsByTagName('styleSheet')[0];
    const fonts = stylesXml.getElementsByTagName('fonts')[0];
    styleSheet.insertBefore(numFmts, fonts);

    return numFmts;
}

function addRupiahStyleOnce(xlsx) {
    const stylesXml = xlsx.xl['styles.xml'];
    const numFmts = ensureNumFmts(stylesXml);

    const numFmtId = '176';

    const formatCode = '"Rp."\\ #,##0;[Red]"Rp."\\ -#,##0';

    const existing = Array.from(stylesXml.getElementsByTagName('numFmt'))
        .find(n => n.getAttribute('numFmtId') === numFmtId);

    if (!existing) {
        const numFmt = stylesXml.createElement('numFmt');
        numFmt.setAttribute('numFmtId', numFmtId);
        numFmt.setAttribute('formatCode', formatCode);
        numFmts.appendChild(numFmt);

        numFmts.setAttribute(
            'count',
            String(parseInt(numFmts.getAttribute('count') || '0', 10) + 1)
        );
    }

    // Append xf and return its index
    const cellXfs = stylesXml.getElementsByTagName('cellXfs')[0];
    const xf = stylesXml.createElement('xf');
    xf.setAttribute('numFmtId', numFmtId);
    xf.setAttribute('fontId', '0');
    xf.setAttribute('fillId', '0');
    xf.setAttribute('borderId', '0');
    xf.setAttribute('xfId', '0');
    xf.setAttribute('applyNumberFormat', '1');

    cellXfs.appendChild(xf);

    const xfCount = cellXfs.getElementsByTagName('xf').length;
    cellXfs.setAttribute('count', String(xfCount));

    return xfCount - 1;
}

function addBoldHeaderStyleOnce(xlsx) {
    const stylesXml = xlsx.xl['styles.xml'];
    const fonts = stylesXml.getElementsByTagName('fonts')[0];

    const font = stylesXml.createElement('font');
    font.appendChild(stylesXml.createElement('b'));
    fonts.appendChild(font);
    fonts.setAttribute('count', String(fonts.getElementsByTagName('font').length));

    const fontId = fonts.getElementsByTagName('font').length - 1;

    const cellXfs = stylesXml.getElementsByTagName('cellXfs')[0];
    const xf = stylesXml.createElement('xf');
    xf.setAttribute('numFmtId', '0');
    xf.setAttribute('fontId', String(fontId));
    xf.setAttribute('fillId', '0');
    xf.setAttribute('borderId', '0');
    xf.setAttribute('xfId', '0');
    xf.setAttribute('applyFont', '1');

    cellXfs.appendChild(xf);
    const xfCount = cellXfs.getElementsByTagName('xf').length;
    cellXfs.setAttribute('count', String(xfCount));

    return xfCount - 1;
}

function applyBoldHeaderRow(sheet, styleIndex) {
    $('row:first c', sheet).attr('s', String(styleIndex));
}

function applyStyleToColumns(sheetXml, styleIndex, targetColumnIndexes) {
    $('row c[r]', sheetXml).each(function () {
        const cell = $(this);
        const ref = cell.attr('r');         // e.g. "C5"
        const colLetters = ref.replace(/[0-9]/g, ''); // "C"

        const colIndex = colLetters.charCodeAt(0) - 65;

        if (targetColumnIndexes.includes(colIndex)) {
            cell.attr('s', String(styleIndex));
            cell.removeAttr('t');
        }
    });
}

function dateToExcelSerial(jsDate) {
    const y = jsDate.getFullYear();
    const m = jsDate.getMonth();
    const d = jsDate.getDate();
    const h = jsDate.getHours();
    const min = jsDate.getMinutes();
    const s = jsDate.getSeconds();

    const dateUtc = Date.UTC(y, m, d);
    const epochUtc = Date.UTC(1899, 11, 30);
    const days = (dateUtc - epochUtc) / 86400000;
    const timeFraction = (h * 3600 + min * 60 + s) / 86400;

    return days + timeFraction;
}

const EXCEL_DATE_FORMATS = {
    'basicdate':  { code: 'd mmmm yyyy',                   id: '177' },
    'date':       { code: 'dddd", "d mmmm yyyy',           id: '178' },
    'dateformat': { code: 'dddd", "d mmmm yyyy',           id: '178' },
    'timestamp':  { code: 'dddd", "d mmmm yyyy hh:mm',     id: '179' },
    'datetime':   { code: 'dddd", "d mmmm yyyy hh:mm',     id: '179' },
};

function addExcelDateStyle(xlsx, formatCode, numFmtId) {
    const stylesXml = xlsx.xl['styles.xml'];
    const numFmts = ensureNumFmts(stylesXml);

    const existing = Array.from(stylesXml.getElementsByTagName('numFmt'))
        .find(n => n.getAttribute('numFmtId') === numFmtId);
    if (!existing) {
        const numFmt = stylesXml.createElement('numFmt');
        numFmt.setAttribute('numFmtId', numFmtId);
        numFmt.setAttribute('formatCode', formatCode);
        numFmts.appendChild(numFmt);
        numFmts.setAttribute('count',
            String(parseInt(numFmts.getAttribute('count') || '0', 10) + 1));
    }

    const cellXfs = stylesXml.getElementsByTagName('cellXfs')[0];
    const xf = stylesXml.createElement('xf');
    xf.setAttribute('numFmtId', numFmtId);
    xf.setAttribute('fontId', '0');
    xf.setAttribute('fillId', '0');
    xf.setAttribute('borderId', '0');
    xf.setAttribute('xfId', '0');
    xf.setAttribute('applyNumberFormat', '1');

    cellXfs.appendChild(xf);
    const xfCount = cellXfs.getElementsByTagName('xf').length;
    cellXfs.setAttribute('count', String(xfCount));

    return xfCount - 1;
}

function applyExcelDateStyles(xlsx, sheet, dataColumns) {
    const rows = $('row', sheet);
    const styleCache = {};

    let excelIdx = 0;
    dataColumns.forEach(col => {
        if (col.exportable !== true) return;
        const ct = col.columnType?.toLowerCase();
        const fmt = ct ? EXCEL_DATE_FORMATS[ct] : null;

        if (fmt) {
            if (!styleCache[fmt.id]) {
                styleCache[fmt.id] = addExcelDateStyle(xlsx, fmt.code, fmt.id);
            }
            const styleIdx = String(styleCache[fmt.id]);

            rows.each(function (rowIdx) {
                if (rowIdx === 0) return;
                const cell = $('c', this).eq(excelIdx);
                if (cell.length) {
                    cell.attr('s', styleIdx);
                    cell.removeAttr('t');
                }
            });
        }
        excelIdx++;
    });
}

function getExcelColumnName(index) {
    let columnName = '';
    let dividend = index + 1;
    while (dividend > 0) {
        const modulo = (dividend - 1) % 26;
        columnName = String.fromCharCode(65 + modulo) + columnName;
        dividend = Math.floor((dividend - modulo) / 26);
    }
    return columnName;
}

function parseExcelCurrencyValue(raw) {
    if (raw === null || raw === undefined) {
        return 0;
    }
    if (typeof raw === 'number' && Number.isFinite(raw)) {
        return raw;
    }
    const cleaned = String(raw)
        .replace(/Rp\.?\s*/gi, '')
        .replace(/\s/g, '')
        .replace(/\./g, '')
        .replace(/,/g, '')
        .replace(/[^\d\-]/g, '');
    const parsed = Number(cleaned);
    return Number.isFinite(parsed) ? parsed : 0;
}

function getDuplicateExportColumns(dataColumns) {
    const result = [];
    let excelIdx = 0;
    dataColumns.forEach(col => {
        if (col.exportable !== true) return;
        if (col.duplicate === true) result.push(excelIdx);
        excelIdx++;
    });
    return result;
}

function getExcelCellValue(cell, xlsx) {
    const $cell = $(cell);
    const cellType = $cell.attr('t');
    const v = $('v', $cell);
    if (cellType === 's' && v.length && xlsx && xlsx.xl && xlsx.xl['sharedStrings.xml']) {
        const sharedIdx = parseInt(v.text(), 10);
        if (Number.isFinite(sharedIdx)) {
            const si = $('si', xlsx.xl['sharedStrings.xml']).eq(sharedIdx);
            if (si.length) {
                return si.text();
            }
        }
    }
    if (v.length) return v.text();
    const is = $('is', $cell);
    if (is.length) return is.text();
    return '';
}

function clearExcelCell(cell) {
    $(cell).children().remove();
}

function findExcelCellInRow(rowEl, colName, rowNum) {
    const cellRef = `${colName}${rowNum}`;
    const cells = rowEl.getElementsByTagName
        ? rowEl.getElementsByTagName('c')
        : [];
    for (let i = 0; i < cells.length; i++) {
        if ((cells[i].getAttribute('r') || '') === cellRef) {
            return cells[i];
        }
    }
    for (let i = 0; i < cells.length; i++) {
        const ref = cells[i].getAttribute('r') || '';
        if (ref.replace(/[0-9]/g, '') === colName) {
            return cells[i];
        }
    }
    return null;
}

function appendExcelCurrencyTotalRow(xlsx, sheet, dataColumns, options = {}) {
    try {
        const exportable = (dataColumns || []).filter((col) => col.exportable === true);
        if (!exportable.length) {
            return;
        }

        const currencyIndexes = [];
        exportable.forEach((col, idx) => {
            const type = String(col.columnType || '').toLowerCase();
            if (type === 'currency' || type === 'money' || type === 'rupiah') {
                currencyIndexes.push(idx);
            }
        });

        if (!currencyIndexes.length) {
            return;
        }

        const sheetDoc = sheet.nodeType === 9 ? sheet : (sheet.ownerDocument || sheet);
        const sheetData = sheet.getElementsByTagName
            ? (sheet.getElementsByTagName('sheetData')[0] || null)
            : null;
        const $sheetData = sheetData ? $(sheetData) : $('sheetData', sheet);
        const dataNode = sheetData || $sheetData.get(0);
        if (!dataNode) {
            return;
        }

        const rows = dataNode.getElementsByTagName('row');
        if (!rows || rows.length < 2) {
            return;
        }

        const totals = {};
        currencyIndexes.forEach((idx) => {
            totals[idx] = 0;
        });

        for (let r = 1; r < rows.length; r++) {
            const rowEl = rows[r];
            const rowNum = rowEl.getAttribute('r') || String(r + 1);
            currencyIndexes.forEach((idx) => {
                const colName = getExcelColumnName(idx);
                const cell = findExcelCellInRow(rowEl, colName, rowNum);
                if (!cell) {
                    return;
                }
                totals[idx] += parseExcelCurrencyValue(getExcelCellValue(cell, xlsx));
            });
        }

        const lastRowAttr = parseInt(rows[rows.length - 1].getAttribute('r') || String(rows.length), 10);
        const totalRowNumber = lastRowAttr + 1;

        let rowXml = `<row r="${totalRowNumber}">`;
        exportable.forEach((col, idx) => {
            const colName = getExcelColumnName(idx);
            const cellRef = `${colName}${totalRowNumber}`;
            if (idx === 0) {
                rowXml += `<c r="${cellRef}" t="inlineStr"><is><t>TOTAL</t></is></c>`;
                return;
            }
            if (currencyIndexes.includes(idx)) {
                rowXml += `<c r="${cellRef}"><v>${totals[idx]}</v></c>`;
                return;
            }
            rowXml += `<c r="${cellRef}" t="inlineStr"><is><t></t></is></c>`;
        });
        rowXml += '</row>';

        const parsed = new DOMParser().parseFromString(rowXml, 'application/xml');
        if (parsed.getElementsByTagName('parsererror').length) {
            // Fallback jQuery append
            $sheetData.append(rowXml);
        } else {
            const newRow = parsed.documentElement;
            const imported = sheetDoc.importNode(newRow, true);
            dataNode.appendChild(imported);
        }

        const rupiahStyleIndex = addRupiahStyleOnce(xlsx);
        const boldStyleIndex = addBoldHeaderStyleOnce(xlsx);

        currencyIndexes.forEach((idx) => {
            const colName = getExcelColumnName(idx);
            const cell = findExcelCellInRow(dataNode.lastChild, colName, String(totalRowNumber))
                || dataNode.querySelector(`c[r="${colName}${totalRowNumber}"]`);
            if (cell) {
                cell.setAttribute('s', String(rupiahStyleIndex));
                cell.removeAttribute('t');
            }
        });

        const labelCell = findExcelCellInRow(dataNode.lastChild, getExcelColumnName(0), String(totalRowNumber))
            || dataNode.querySelector(`c[r="${getExcelColumnName(0)}${totalRowNumber}"]`);
        if (labelCell) {
            labelCell.setAttribute('s', String(boldStyleIndex));
        }
    } catch (e) {
        console.error('appendExcelCurrencyTotalRow failed', e);
    }
}

function getVerticalTopStyleForCell(xlsx, cell) {
    const stylesXml = xlsx.xl['styles.xml'];
    const cellXfs = stylesXml.getElementsByTagName('cellXfs')[0];
    const xfElements = cellXfs.getElementsByTagName('xf');

    const existingIdx = parseInt($(cell).attr('s') || '0');
    const baseXf = xfElements[existingIdx];

    const xf = baseXf ? baseXf.cloneNode(true) : stylesXml.createElement('xf');
    if (!baseXf) {
        xf.setAttribute('numFmtId', '0');
        xf.setAttribute('fontId', '0');
        xf.setAttribute('fillId', '0');
        xf.setAttribute('borderId', '0');
        xf.setAttribute('xfId', '0');
    }
    xf.setAttribute('applyAlignment', '1');

    let alignment = xf.getElementsByTagName('alignment')[0];
    if (!alignment) {
        alignment = stylesXml.createElement('alignment');
        xf.appendChild(alignment);
    }
    alignment.setAttribute('vertical', 'top');
    alignment.setAttribute('wrapText', '1');

    cellXfs.appendChild(xf);
    const xfCount = xfElements.length;
    cellXfs.setAttribute('count', String(xfCount));

    return xfCount - 1;
}

function mergeExcelDuplicates(xlsx, sheet, duplicateCols) {
    const rows = $('row', sheet);
    const mergeRefs = [];

    duplicateCols.forEach(colIdx => {
        let colName = null;
        let lastValue = null;
        let groupStartRow = null;
        let lastExcelRow = null;
        let groupStartCell = null;

        rows.each(function (rowIdx) {
            if (rowIdx === 0) return;

            const excelRow = parseInt($(this).attr('r'));
            const cell = $('c', this).eq(colIdx);
            if (!cell.length) return;

            if (!colName) {
                colName = cell.attr('r').replace(/[0-9]/g, '');
            }

            const value = getExcelCellValue(cell);

            if (value !== lastValue) {
                if (groupStartCell && lastExcelRow && groupStartRow !== lastExcelRow) {
                    mergeRefs.push(colName + groupStartRow + ':' + colName + lastExcelRow);
                    const styleIdx = getVerticalTopStyleForCell(xlsx, groupStartCell);
                    groupStartCell.attr('s', String(styleIdx));
                }
                lastValue = value;
                groupStartRow = excelRow;
                groupStartCell = cell;
            } else {
                clearExcelCell(cell);
            }
            lastExcelRow = excelRow;
        });

        if (colName && lastExcelRow && groupStartRow !== lastExcelRow) {
            mergeRefs.push(colName + groupStartRow + ':' + colName + lastExcelRow);
            if (groupStartCell) {
                const styleIdx = getVerticalTopStyleForCell(xlsx, groupStartCell);
                groupStartCell.attr('s', String(styleIdx));
            }
        }
    });

    if (mergeRefs.length > 0) {
        let mergeCells = $('mergeCells', sheet);
        if (mergeCells.length === 0) {
            $('sheetData', sheet).after('<mergeCells></mergeCells>');
            mergeCells = $('mergeCells', sheet);
        }
        const existingCount = parseInt(mergeCells.attr('count') || '0');
        mergeRefs.forEach(ref => {
            mergeCells.append('<mergeCell ref="' + ref + '"/>');
        });
        mergeCells.attr('count', existingCount + mergeRefs.length);
    }
}

function mergePdfDuplicates(doc, duplicateCols) {
    const table = doc.content.find(c => c.table);
    if (!table) return;

    const body = table.table.body;

    duplicateCols.forEach(colIdx => {
        let groupStart = null;
        let groupValue = null;

        for (let rowIdx = 1; rowIdx < body.length; rowIdx++) {
            const cell = body[rowIdx][colIdx];
            const value = (cell && typeof cell === 'object') ? String(cell.text ?? '') : String(cell ?? '');

            if (value !== groupValue) {
                if (groupStart !== null && rowIdx - groupStart > 1) {
                    const first = body[groupStart][colIdx];
                    if (first && typeof first === 'object') {
                        first.rowSpan = rowIdx - groupStart;
                    } else {
                        body[groupStart][colIdx] = {text: first ?? '', rowSpan: rowIdx - groupStart};
                    }
                    for (let i = groupStart + 1; i < rowIdx; i++) {
                        body[i][colIdx] = {};
                    }
                }
                groupStart = rowIdx;
                groupValue = value;
            }
        }

        if (groupStart !== null && body.length - groupStart > 1) {
            const first = body[groupStart][colIdx];
            if (first && typeof first === 'object') {
                first.rowSpan = body.length - groupStart;
            } else {
                body[groupStart][colIdx] = {text: first ?? '', rowSpan: body.length - groupStart};
            }
            for (let i = groupStart + 1; i < body.length; i++) {
                body[i][colIdx] = {};
            }
        }
    });
}

function addCustomNumberFormat(xlsx, numberFormat) {

    //kodingan seko stackoverflow ramudeng njir
    let numFmtsElement = xlsx.xl['styles.xml'].getElementsByTagName('numFmts')[0];
    let celXfsElement = xlsx.xl['styles.xml'].getElementsByTagName('cellXfs')[0];

    // Define the Rupiah custom format
    const rupiahFormat = 'Rp.\\ #,##0;[Red]Rp.\\ -#,##0';

    // Check if `numFmts` already exists, otherwise create it
    if (!numFmtsElement) {
        const stylesXml = xlsx.xl['styles.xml'];
        const newNumFmtsElement = stylesXml.createElement('numFmts');
        newNumFmtsElement.setAttribute('count', '1');
        stylesXml.documentElement.getElementsByTagName('styleSheet')[0].appendChild(newNumFmtsElement);
        numFmtsElement = newNumFmtsElement;
    }

    // Add the custom number format
    const numFmtElement = xlsx.xl['styles.xml'].createElement('numFmt');
    numFmtElement.setAttribute('numFmtId', '176'); // Ensure this ID is not already used
    numFmtElement.setAttribute('formatCode', rupiahFormat);
    numFmtsElement.appendChild(numFmtElement);

    // Update the count attribute
    const currentNumFmtsCount = parseInt(numFmtsElement.getAttribute('count') || '0', 10);
    numFmtsElement.setAttribute('count', currentNumFmtsCount + 1);

    // Add a new cell style using the custom format
    const cellStyle = '<xf numFmtId="176" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/>';
    celXfsElement.innerHTML += cellStyle;

    // Update the count attribute for `cellXfs`
    const currentCellXfsCount = parseInt(celXfsElement.getAttribute('count') || '0', 10);
    celXfsElement.setAttribute('count', currentCellXfsCount + 1);
}


function formatTargetColumn(xlsx, col) {
    let sheet = xlsx.xl.worksheets['sheet1.xml'];
    $('row c[r^="' + col + '"]', sheet).attr('s', '68');
}


function newexportaction(e, dt, button, config) {
    let self = this;
    let oldStart = dt.settings()[0]._iDisplayStart;
    let maxLength = dt.settings()[0]._iRecordsDisplay;
    if (maxLength > 1500) {
        e.preventDefault();
        warningAlert('Data terlalu banyak! <hr>pastikan data yang diexport kurang dari 1500 baris')
    } else {
        dt.one('preXhr', function (e, s, data) {
            data.start = 0;
            data.length = maxLength;
            dt.one('preDraw', function (e, settings) {
                if (button[0].className.indexOf('buttons-copy') >= 0) {
                    $.fn.dataTable.ext.buttons.copyHtml5.action.call(self, e, dt, button, config);
                } else if (button[0].className.indexOf('buttons-excel') >= 0) {
                    $.fn.dataTable.ext.buttons.excelHtml5.available(dt, config) ?
                        $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config) :
                        $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
                } else if (button[0].className.indexOf('buttons-csv') >= 0) {
                    $.fn.dataTable.ext.buttons.csvHtml5.available(dt, config) ?
                        $.fn.dataTable.ext.buttons.csvHtml5.action.call(self, e, dt, button, config) :
                        $.fn.dataTable.ext.buttons.csvFlash.action.call(self, e, dt, button, config);
                } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
                    $.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config) ?
                        $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config) :
                        $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
                } else if (button[0].className.indexOf('buttons-print') >= 0) {
                    $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
                }
                dt.one('preXhr', function (e, s, data) {
                    settings._iDisplayStart = oldStart;
                    data.start = oldStart;
                });
                setTimeout(dt.ajax.reload, 0);
                return false;
            });
        });
        dt.ajax.reload();
    }
}

function dtButtons(options, buttons) {
    // Button configurations
    const buttonConfigMap = {
        copy: {
            extend: 'copy',
            title: '',
            text: '<i class="ri ri-file-copy-2-line me-2"></i>Copy',
            exportOptions: {
                columns: ':visible:not(.no-export)'
            },
        },
        excel: {
            extend: 'excel',
            title: '',
            text: '<i class="ri ri-file-excel-line me-2"></i>Excel',
            exportOptions: {
                columns: ':visible:not(.no-export)'
            },
            customize: function (xlsx) {
                const sheet = xlsx.xl.worksheets['sheet1.xml'];
                const rupiahStyleIndex = addRupiahStyleOnce(xlsx);

                const currencyColumns = [];
                let excelColIdx = 0;
                options.dataColumns.forEach(col => {
                    if (col.exportable !== true) return;
                    if (col.columnType === 'currency' || col.columnType === 'money' || col.columnType === 'rupiah') {
                        currencyColumns.push(excelColIdx);
                    }
                    excelColIdx++;
                });

                applyStyleToColumns(sheet, rupiahStyleIndex, currencyColumns);
                applyExcelDateStyles(xlsx, sheet, options.dataColumns);

                const duplicateCols = getDuplicateExportColumns(options.dataColumns);
                mergeExcelDuplicates(xlsx, sheet, duplicateCols);

                const boldHeaderStyleIndex = addBoldHeaderStyleOnce(xlsx);
                applyBoldHeaderRow(sheet, boldHeaderStyleIndex);

                if (options.excelCurrencyTotal) {
                    appendExcelCurrencyTotalRow(xlsx, sheet, options.dataColumns, options);
                }
            },
        },
        pdf: {
            extend: 'pdf',
            title: '',
            text: '<i class="ri ri-file-pdf-2-line me-2"></i>Pdf',
            modifier: {page: 'all'},
            orientation: options.pdfOrientation || 'portrait',
            pageSize: options.pdfPageSize || 'A4',
            exportOptions: {
                columns: ':visible:not(.no-export)',
            },
            customize: function (doc) {
                const exportableCount = options.dataColumns.filter(col => col.exportable === true).length;
                const LANDSCAPE_THRESHOLD = 8;

                if (options.pdfOrientation) {
                    doc.pageOrientation = options.pdfOrientation;
                } else if (exportableCount > LANDSCAPE_THRESHOLD) {
                    doc.pageOrientation = 'landscape';
                }
                if (options.pdfPageSize) {
                    doc.pageSize = options.pdfPageSize;
                }
                doc.pageMargins = options.pdfMargins || [10, 10, 10, 10];
                doc.defaultStyle.fontSize = options.pdfFontSize ?? 7;
                const tableNode = doc.content.find(n => n.table);
                if (tableNode && tableNode.table && tableNode.table.body) {
                    if (options.pdfHeaderFontSize && tableNode.table.body[0]) {
                        tableNode.table.body[0].forEach(cell => {
                            if (cell && typeof cell === 'object') {
                                cell.fontSize = options.pdfHeaderFontSize;
                                cell.bold = true;
                            }
                        });
                    }
                    const colCount = tableNode.table.body[0].length;
                    const pageWidth = doc.pageOrientation === 'landscape' ? 842 : 595;
                    const margins = doc.pageMargins || [10, 10, 10, 10];
                    const usableWidth = pageWidth - margins[0] - margins[2] - 16;
                    const colWidth = Math.max(usableWidth / colCount, 28);
                    const exportableColumns = options.dataColumns.filter(col => col.exportable === true);

                    if (options.pdfColumnWidths && exportableColumns.length === colCount) {
                        tableNode.table.widths = exportableColumns.map(col => {
                            const configured = options.pdfColumnWidths[col.data ?? 'no'];
                            if (configured === '*') {
                                return '*';
                            }
                            if (typeof configured === 'number') {
                                return configured;
                            }
                            return colWidth;
                        });
                    } else if (options.pdfWidths && options.pdfWidths.length === colCount) {
                        tableNode.table.widths = options.pdfWidths.map(width => {
                            if (width === '*' || width === 'auto') {
                                return width;
                            }
                            return Number(width) || colWidth;
                        });
                    } else {
                        tableNode.table.widths = Array(colCount).fill(colWidth);
                    }

                    for (let rowIndex = 0; rowIndex < tableNode.table.body.length; rowIndex++) {
                        tableNode.table.body[rowIndex].forEach(cell => {
                            if (cell && typeof cell === 'object') {
                                cell.noWrap = false;
                            }
                        });
                    }
                    tableNode.layout = {
                        hLineWidth: () => 0.5,
                        vLineWidth: () => 0.5,
                        paddingLeft: () => 2,
                        paddingRight: () => 2,
                        paddingTop: () => 2,
                        paddingBottom: () => 2,
                    };
                }
                const duplicateCols = getDuplicateExportColumns(options.dataColumns);
                mergePdfDuplicates(doc, duplicateCols);
            }
        },
        csv: {
            extend: 'csv',
            title: '',
            text: '<i class="ri ri-file-list-2-line me-2"></i>Csv',
        },
        print: {
            extend: 'print',
            title: '',
            text: '<i class="ri ri-printer-line me-2"></i>Print',
            customize: function (win) {
                const landscape = options.pdfOrientation === 'landscape'
                    || options.dataColumns.filter(col => col.exportable === true).length > 8;
                const fontSize = options.pdfFontSize ?? 8;
                const style = win.document.createElement('style');
                style.innerHTML = `
                    @page { size: ${landscape ? 'landscape' : 'portrait'}; margin: 12mm; }
                    body { font-size: ${fontSize}px; }
                    table { width: 100% !important; font-size: ${fontSize}px !important; table-layout: fixed; }
                    th, td { white-space: normal !important; word-break: break-word; padding: 2px 4px !important; }
                `;
                win.document.head.appendChild(style);
            },
        },
    };

    const buttonConfigs = buttons.map(button => buttonConfigMap[button]).filter(Boolean);

    return buttonConfigs.map(config => ({
        ...config,
        className: 'dropdown-item',
        exportOptions: {
            columns: function (index, data, node) {
                let exportableColumn = options.dataColumns[index]?.exportable;
                return exportableColumn === true;
            }, format: {
                header: function (data, columnIdx) {
                    if (typeof data === 'string' && data.includes('select-all')) {
                        return 'NO';
                    }
                    return data.toUpperCase();
                },
                body: function (data, row, column, node) {
                    if (data === null) return '';
                    const exportableColumns = options.dataColumns.filter(col => col.exportable === true);
                    const columnInfo = exportableColumns[column];
                    let columnType = columnInfo.columnType;

                    const numberColumn = columnInfo.numberColumn;
                    // let rawData = table.row(row).data();
                    // console.log(rawData)
                    // console.log(exportableColumns)

                    if (columnType !== null) {
                        switch (columnType.toLowerCase()) {
                            case 'row':
                            case 'number':
                            case 'no':
                                return row + 1;
                            case 'basicdate':
                                if (!data) return '';
                                if (config.extend === 'excel') {
                                    return dateToExcelSerial(new Date(data));
                                }
                                let basicDate = new Date(data);
                                let basicDateOptions = {
                                    day: 'numeric',
                                    month: 'long',
                                    year: 'numeric'
                                };
                                return (typeof formatTanggalIndonesia === 'function') ? formatTanggalIndonesia(basicDate) : basicDate.toLocaleDateString('id-ID', basicDateOptions);
                            case 'date':
                            case 'dateformat':
                                if (!data) return '';
                                if (config.extend === 'excel') {
                                    return dateToExcelSerial(new Date(data));
                                }
                                let date = new Date(data);
                                let dateOptions = {
                                    weekday: 'long',
                                    day: 'numeric',
                                    month: 'long',
                                    year: 'numeric'
                                };
                                return (typeof formatTanggalIndonesia === 'function') ? formatTanggalIndonesia(date) : date.toLocaleDateString('id-ID', dateOptions);
                            case 'timestamp':
                            case 'datetime':
                                if (!data || data === '0000-00-00 00:00:00' || data === '0000-00-00') {
                                    return '';
                                }
                                if (config.extend === 'excel') {
                                    const excelDate = new Date(data);
                                    if (Number.isNaN(excelDate.getTime())) {
                                        return '';
                                    }
                                    return dateToExcelSerial(excelDate);
                                }
                                const tsDate = new Date(data);
                                if (Number.isNaN(tsDate.getTime())) {
                                    return '';
                                }
                                const tsOptions = {
                                    weekday: 'long',
                                    day: 'numeric',
                                    month: 'long',
                                    year: 'numeric',
                                    hour: 'numeric',
                                    minute: 'numeric'
                                };
                                return (typeof formatTanggalIndonesia === 'function') ? formatTanggalIndonesia(tsDate, { withTime: true }) : tsDate.toLocaleDateString('id-ID', tsOptions);
                            case 'periode':
                            case 'yearmonth':
                                if (!data || typeof data !== 'string' || data.length !== 6 || !/^\d{6}$/.test(data)) {
                                    return '';
                                }
                                const monthsID = [
                                    "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                                    "Juli", "Agustus", "September", "Oktober", "November", "Desember"
                                ];
                                const pmYear = Math.floor(data / 100);
                                const pmMonth = data % 100;
                                return (pmMonth >= 1 && pmMonth <= 12) ? `${monthsID[pmMonth - 1]} ${pmYear}` : '';
                            case 'currency':
                                if (config.extend !== 'excel') {
                                    return new Intl.NumberFormat('id-ID', {
                                        style: 'currency',
                                        currency: 'IDR',
                                    }).format(data);
                                }
                                return data;
                        }

                    }
                    if (config.extend === "excel" && !numberColumn) {
                        return "\0" + data;
                    }
                    if (data.length <= 0) return data
                    // if (config.extend === "excel" && data.length >= 10 && !isNaN(parseFloat(data)) && isFinite(data)) {
                    //     return "\0" + data;
                    // }
                    let el = $.parseHTML(data);
                    let result = '';
                    $.each(el, function (index, item) {
                        if (item.classList && item.classList.contains('user-name')) {
                            result += item.lastChild.firstChild.textContent;
                        } else {
                            result += item.innerText || item.textContent;
                        }
                    });
                    return result;
                }
            },
            modifier: config.modifier,
            orthogonal: 'export'
        },
        action: newexportaction
    }));
}

function formatColumnName(columnName) {
    return columnName.replace(/_/g, ' ').replace(/\b\w/g, match => match.toUpperCase());
}

function createColumnsHtml(columns) {
    return columns.map(column => `<th>${formatColumnName(column.name)}</th>`).join('');
}

function createColumns(id, columns, location) {
    const table = document.getElementById(id);
    let headerOrFooter = table.querySelector(location);
    if (!headerOrFooter) {
        headerOrFooter = document.createElement(location);
        headerOrFooter.classList.add('table-light');
        table.appendChild(headerOrFooter);
    }
    headerOrFooter.innerHTML = '';
    const row = document.createElement('tr');
    row.classList.add('text-start', 'fw-bold', 'fs-7', 'text-uppercase', 'gs-0');
    row.innerHTML = createColumnsHtml(columns);
    headerOrFooter.appendChild(row);
}

/** Siapkan <tfoot> kosong — jangan duplikasi label header (hanya baris TOTAL dari footerCallback). */
function prepareTableFoot(id) {
    const table = document.getElementById(id);
    let tfoot = table.querySelector('tfoot');
    if (!tfoot) {
        tfoot = document.createElement('tfoot');
        tfoot.classList.add('table-light');
        table.appendChild(tfoot);
    }
    tfoot.innerHTML = '';
}

let DT = {};
const languageKey = 'datatables_id_language';
const languageUrl = 'https://cdn.datatables.net/plug-ins/2.0.6/i18n/id.json';

async function fetchLanguageFile() {
    try {
        const response = await fetch(languageUrl);
        if (!response.ok) throw new Error('Network response was not ok');
        const data = await response.json();
        localStorage.setItem(languageKey, JSON.stringify(data)); // Save to localStorage
        return data;
    } catch (error) {
        console.error('Error fetching language file:', error);
        return null;
    }
}

async function dataTableCreate(options) {
    let idTable = $(`#${options.tableId}`);
    let searchPanel = [];
    let buttonsConfig = Array.isArray(options.buttons) && options.buttons.length > 0 ? options.buttons : false;
    const buttonDom = `${(buttonsConfig ? '<"row pb-3"<"dt-action-buttons d-flex justify-content-center justify-content-md-end px-5 px-md-3"B>">' : '')}`;
    const dom = `${buttonDom}<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"row dt-row"<"table-responsive"t>r><"row"<"col-sm-12 col-md-6 text-wrap"i><"col-sm-12 col-md-6"p>>`;

    let languageData = localStorage.getItem(languageKey);

    if (!languageData) {
        languageData = await fetchLanguageFile();
    } else {
        languageData = JSON.parse(languageData);
    }

    DT[`${options.tableId}`] = idTable.DataTable({
        autoWidth: false,
        responsive: false,
        columns: options.dataColumns,
        fixedHeader: options.fixedHeader ?? false,
        scrollX: options.scrollX ?? false,
        searching: options.searching || false,
        processing: true,
        // rowId: 'item_id',
        serverSide: options.serverSide ?? true,
        order: options.order ?? [],
        paging: options.paging ?? true,
        pageLength: options.pageLength ?? 10,
        lengthMenu: options.lengthMenu ?? [10, 25, 50, 75, 100],
        retrieve: options.retrieve ?? false,
        cache: options.cache ?? false,
        select: options.select
            ? options.select === 'multi'
                ? {
                    style: 'multi',
                    selector: 'td:not(.exclude-selection)'
                }
                : {
                    style: 'single',
                    selector: 'td:not(.exclude-selection)',
                }
            : false,
        columnDefs: [
            {
                targets: 0,
                searchable: false,
                orderable: false,
                className: options.select ? 'no-export' : ' table_dt_no',
                checkboxes: options.select ? options.select === 'multi'
                        ? {
                            selectRow: true,
                            selectAllRender: '<input type="checkbox" class="form-check-input select-all">'
                        }
                        : {
                            selectRow: true,
                            selectAll: false,
                        }
                    : false,
            }
        ],
        buttons: buttonsConfig && [
            {
                extend: 'collection',
                className: 'btn btn-vimeo dropdown-toggle me-2',
                text: '<i class="ri ri-export-line me-2"></i>Export',
                buttons: [
                    dtButtons(options, buttonsConfig)
                ],
            }
        ],
        language: {
            ...languageData,
            processing: 'Memuat Data...'
        },
        dom: dom,
        ajax: {
            url: options.dataUrl,
            type: "GET",
            data: function (d) {
                if (options.formId) {
                    let transformedData = options.formId ? $(`#${options.formId}`).serializeArray().reduce((acc, {
                        name,
                        value
                    }) => {
                        if (acc[name]) {
                            if (!Array.isArray(acc[name])) {
                                acc[name] = [acc[name]];
                            }
                            if (Array.isArray(value)) {
                                acc[name] = acc[name].concat(value);
                            } else {
                                acc[name].push(value);
                            }
                        } else {
                            acc[name] = value;
                        }
                        return acc;
                    }, {}) : {};
                    return $.extend({}, d, transformedData);
                }
            }, error: function (xhr, error, code) {
                if (xhr && (xhr.status === 401 || xhr.status === 419)) {
                    if (typeof window.pingKeepAlive === 'function') {
                        window.pingKeepAlive();
                    }
                    return;
                }
                const descriptions = {
                    '404': 'Data tidak ditemukan!',
                    '500': 'Internal Server Error',
                };
                errorAlert(descriptions[xhr.status] || 'Ada masalah saat mengambil data dari server, Silahkan muat ulang halaman');
            }
        },
        preDrawCallback: function (settings) {
            if (options.formId) {
                let submitButton = $(`#${options.formId} input[type="submit"], #${options.formId} button[type="submit"]`);
                let resetButton = $(`#${options.formId} input[type="reset"], #${options.formId} button[type="reset"]`);

                if (submitButton.length !== 0) {
                    const buttonHtml = submitButton.hasClass('btn-bayar')
                        ? `<span class="spinner-border me-2" role="status" aria-hidden="true">Bayar`
                        : `<span class="spinner-border me-2" role="status" aria-hidden="true"></span>Cari`;
                    submitButton.html(buttonHtml).prop('disabled', true);
                }
                if (resetButton.length !== 0) {
                    resetButton.prop('disabled', true);
                    resetButton.html(`<span class="spinner-border me-2" role="status" aria-hidden="true"></span>Reset`);
                }
            }
        },
        createdRow: function (row, data, dataIndex) {
            $(row).find('td').each(function (cellIndex) {
                const columnConfig = options.dataColumns[cellIndex];
                if (columnConfig.excludeFromSelection) {
                    $(this).addClass('exclude-selection'); // Add a class to exclude
                }
            });
        },
        drawCallback: function (settings) {
            let api = this.api();
            let rows = api.rows({page: 'current'}).nodes();

            const duplicateColumns = api.settings()[0].aoColumns
                .map((col, idx) => ({
                    idx,
                    duplicate: col.duplicate === true,
                }))
                .filter(c => c.duplicate);

            duplicateColumns.forEach(col => {
                let lastValue = null;
                let lastCell = null;

                api.column(col.idx, {page: 'current'}).data().each((value, rowIndex) => {
                    const cell = $('td', rows[rowIndex]).eq(col.idx);

                    cell.removeClass(
                        'dt-duplicate-hidden dt-duplicate-top dt-duplicate-restore'
                    );
                    if (rowIndex === 0 || value !== lastValue) {
                        if (rowIndex !== 0 && lastCell) {
                            cell.addClass('dt-duplicate-restore');
                        }
                        lastValue = value;
                        lastCell = cell;
                        return;
                    }

                    cell
                        .html('')
                        .addClass('dt-duplicate-hidden');

                    lastCell.addClass('dt-duplicate-top');
                    lastCell = cell;
                });
            });

            let labelNo = $(idTable.DataTable().table().header()).find('th').eq(0);
            labelNo && labelNo.removeClass('sorting_asc');

            if (options.formId) {
                let submitButton = $(`#${options.formId} input[type="submit"], #${options.formId} button[type="submit"]`);
                let resetButton = $(`#${options.formId} input[type="reset"], #${options.formId} button[type="reset"]`);
                if (submitButton.length !== 0) {
                    const buttonHtml = submitButton.hasClass('btn-bayar')
                        ? `<span class="ri-cash-line me-2"></span>Bayar`
                        : `<span class="ri-search-line me-2"></span>Cari`;
                    submitButton.html(buttonHtml).prop('disabled', false);
                }
                if (resetButton.length !== 0) {
                    resetButton.html(`<span class="ri-reset-left-line me-2"></span>Reset`);
                    resetButton.prop('disabled', false);
                }
            }
        },
        initComplete: function (data) {
            //// for fixed header only
            if (options.fixedHeader) {
                if (window.Helpers.isNavbarFixed()) {
                    let navHeight = $('#layout-navbar').outerHeight();
                    new $.fn.dataTable.FixedHeader($(idTable).dataTable()).headerOffset(navHeight);
                } else {
                    new $.fn.dataTable.FixedHeader($(idTable).dataTable());
                }

                if (options.scrollX) {
                    let isHeaderRestored = false;
                    $(window).on('scroll', function () {
                        let fixedHeaderElement = $('.fixedHeader-floating');
                        let tableHeaderOffset = $(`#${options.tableId} thead`).offset().top;
                        let scrollPosition = $(window).scrollTop();

                        if (scrollPosition <= tableHeaderOffset) {
                            if (!fixedHeaderElement.is(':visible') && !isHeaderRestored) {
                                setTimeout(function () {
                                    idTable.DataTable().columns.adjust()
                                }, 250)
                                isHeaderRestored = true;
                            }
                        } else {
                            if (isHeaderRestored) {
                                isHeaderRestored = false;
                            }
                        }
                    });
                }
            }

            setTimeout(function () {
                let labelNo = $(idTable.DataTable().table().header()).find('th').eq(0);
                labelNo && labelNo.removeClass('sorting_asc');

                searchPanel[options.tableId] = $(`#${options.tableId}_filter input`);
                if (searchPanel[options.tableId]) {
                    searchPanel[options.tableId].unbind();
                    searchPanel[options.tableId].bind().on('keyup', debounce(function () {
                        idTable.DataTable().search(this.value).draw();
                    }, 500));
                }
            }, 0)
        },
        createdRow: function (row, data, dataIndex) {
            row.setAttribute('id', `${options.tableId}-row-` + (dataIndex + 1));
        },
        footerCallback: function (row, data, start, end, display) {
            let api = this.api();
            let json = this.api().ajax.json();
            const $footer = $(api.table().footer());
            $footer.empty();
            if (!json || !json.totals) {
                return;
            }
            let totalRow = $('<tr class="total-row"></tr>');
            totalRow.append('<th colspan="2" class="fw-bolder">TOTAL</th>');
            api.columns().every(function (colIdx) {
                if (colIdx >= 2) {
                    let cellVal = '';
                    let value = Object.values(json.totals).find(v => (v.location ?? 1) == colIdx);
                    if (value) {
                        let colVal = value.value ?? 0;
                        let columnType = value.columnType ?? null;
                        if (columnType === 'currency') {
                            colVal = new Intl.NumberFormat('id-ID', {
                                style: "currency",
                                currency: "IDR",
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            }).format(colVal);
                        }
                        cellVal = colVal;
                    }
                    totalRow.append(`<th class="text-end fw-bolder">${cellVal}</th>`);
                }
            });
            $footer.append(totalRow);
        },
        error: function (xhr, error, code) {
            errorAlert('Data tidak dapat dimuat')
        }
    })
}

function dataReload(id = null) {
    id && DT[`${id}`].ajax.reload(null, false);
}

function dataReFilter(id = null, formId = null) {
    id && $(`#${id}`).DataTable().draw();
    // if (id) {
    //     const tableId = $(`#${id}`);
    //     tableId.DataTable().draw();
    // }
}

async function getDT(options) {
    options.dataColumns = Array.isArray(options.dataColumns) ? options.dataColumns : [];

    const finishColumns = function (data) {
                $.each(data, function (index, column) {
                    let columnType;
                    let renderFunc = '';
                    if (column.columnType || column.columntype) {
                        columnType = column.columnType || column.columntype;
                        switch (columnType.toLowerCase()) {
                            case 'row':
                            case 'number':
                            case 'no':
                                renderFunc = function (data, type, row, meta) {
                                    if (type === 'display' || type === 'filter') {
                                        if (options.select) {
                                            let thisValue = data ?? meta.row + meta.settings._iDisplayStart + 1;
                                            return `<input type="checkbox" id="siswa-checkbox-${data}" class="dt-checkboxes form-check-input checkbox-siswa" value="${thisValue}" aria-selected="false">`;
                                        }
                                        return meta.row + meta.settings._iDisplayStart + 1;
                                    } else if (type === 'export') {
                                        return meta.row;
                                    }
                                    return data;
                                }
                                break;
                            case 'suffix':
                                renderFunc = function (data, type, row) {
                                    if (type === 'display' || type === 'filter') {
                                        return data + ' ' + column.suffix;
                                    }
                                    return data;
                                };
                                break;
                            case 'prefix':
                                renderFunc = function (data, type, row) {
                                    if (type === 'display' || type === 'filter') {
                                        return column.prefix + ' ' + data;
                                    }
                                    return data;
                                };
                                break;
                            case 'money':
                            case 'currency':
                                renderFunc = function (data, type, row) {
                                    if (type === 'display' || type === 'filter') {
                                        const value = Number(data);

                                        if (!Number.isFinite(value)) {
                                            return 'Rp. 0';
                                        }

                                        const formatted = $.fn.dataTable
                                            .render
                                            .number('.', ',', 0, 'Rp. ')
                                            .display(Math.abs(value));

                                        return value < 0 ? `Rp. -${formatted.replace('Rp. ', '')}` : formatted;
                                    }
                                    return data;
                                };
                                break;
                            case 'basicdate':
                                renderFunc = function (data, type, row) {
                                    if (type === 'display' || type === 'filter') {
                                        let date = new Date(data);
                                        let options = {
                                            day: 'numeric',
                                            month: 'long',
                                            year: 'numeric'
                                        };
                                        return (typeof formatTanggalIndonesia === 'function') ? formatTanggalIndonesia(date, options && options.hour ? { withTime: true } : undefined) : date.toLocaleDateString('id-ID', options);
                                    }
                                    return data;
                                };
                                break;
                            case 'date':
                            case 'dateformat':
                                renderFunc = function (data, type, row) {
                                    if (type === 'display' || type === 'filter') {
                                        let date = new Date(data);
                                        let options = {
                                            weekday: 'long',
                                            day: 'numeric',
                                            month: 'long',
                                            year: 'numeric'
                                        };
                                        return (typeof formatTanggalIndonesia === 'function') ? formatTanggalIndonesia(date, options && options.hour ? { withTime: true } : undefined) : date.toLocaleDateString('id-ID', options);
                                    }
                                    return data;
                                };
                                break;
                            case 'periode':
                            case 'yearmonth':
                                renderFunc = function (data, type, row) {
                                    if (!data || typeof data !== 'string' || data.length !== 6 || !/^\d{6}$/.test(data)) {
                                        return '';
                                    }
                                    const monthsIndonesian = [
                                        "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                                        "Juli", "Agustus", "September", "Oktober", "November", "Desember"
                                    ];
                                    const year = Math.floor(data / 100);
                                    const month = data % 100;
                                    return (month >= 1 && month <= 12) ? `${monthsIndonesian[month - 1]} ${year}` : '';
                                };
                                break;
                            case 'timestamp':
                            case 'datetime':
                                renderFunc = function (data, type, row) {
                                    if (type === 'display' || type === 'filter') {
                                        if (!data || data === '0000-00-00 00:00:00' || data === '0000-00-00') {
                                            return '';
                                        }
                                        const date = new Date(data);
                                        if (Number.isNaN(date.getTime())) {
                                            return '';
                                        }
                                        const options = {
                                            weekday: 'long',
                                            day: 'numeric',
                                            month: 'long',
                                            year: 'numeric',
                                            hour: 'numeric',
                                            minute: 'numeric'
                                        };
                                        return (typeof formatTanggalIndonesia === 'function') ? formatTanggalIndonesia(date, options && options.hour ? { withTime: true } : undefined) : date.toLocaleDateString('id-ID', options);
                                    }
                                    return data;
                                };
                                break;
                            case 'button':
                                renderFunc = function (data, type, row) {
                                    if (!data || !(type === 'display' || type === 'filter')) {
                                        return '';
                                    }

                                    const {
                                        buttonClass = 'btn',
                                        buttonIcon,
                                        buttonIconSVG,
                                        buttonText,
                                        noCaption,
                                        button,
                                        buttonLink,
                                        dataVal = true,
                                    } = column;

                                    const iconStyle = buttonIcon ? `<i class="${buttonIcon}"></i>` : buttonIconSVG || '';
                                    const resolvedButtonText = column.buttonTextField && row[column.buttonTextField]
                                        ? row[column.buttonTextField]
                                        : buttonText;
                                    const title = resolvedButtonText || '';
                                    const buttonTextContent = noCaption ? '' : resolvedButtonText;
                                    const rowDataJson = dataVal ? JSON.stringify(row).replace(/'/g, "&#39;").replace(/"/g, "&quot;") : null;

                                    const createButton = (attributes, content) => `<button type="button" class="${buttonClass}" title="${title}" ${attributes}>${content}</button>`;

                                    switch (button) {
                                        case 'modal':
                                            return createButton(`data-bs-toggle="modal" data-bs-target="${buttonLink}" ${rowDataJson ? "data-val='" + rowDataJson + "'" : ''}`, `${iconStyle}${buttonTextContent}`);
                                        case 'link':
                                            const link = buttonLink ? buttonLink.replace(':id', row.item_id) : '#';
                                            return `<a class="${buttonClass}" href="${link}" title="${title}">${iconStyle}${buttonTextContent}</a>`;
                                        case 'action':
                                            return createButton(`${rowDataJson ? "data-val='" + rowDataJson + "'" : ''}`, `${iconStyle}${buttonTextContent}`);
                                        default:
                                            return '';
                                    }
                                }
                                break;
                            case 'boolean':
                                renderFunc = function (data, type, row) {
                                    if (type === 'display' || type === 'filter' || type === 'export') {
                                        let trueVal = column.trueVal ?? 'benar';
                                        let falseVal = column.falseVal ?? 'Salah';
                                        if (type === 'export') {
                                            console.log(data, trueVal, falseVal);
                                            if (data === "1" || data === 1 || data === true) {
                                                return trueVal;
                                            } else {
                                                return falseVal;
                                            }
                                        }
                                        if (column.booleanCheck) {
                                            trueVal = '<i class="ri-check-line"></i>';
                                            falseVal = '<i class="ri-close-line"></i>';
                                        }
                                        if (data === "1" || data === 1 || data === true) {
                                            return `<span class="badge px-2 rounded-pill bg-label-success">${trueVal}</span>`
                                        } else {
                                            return `<span class="badge px-2 rounded-pill bg-label-danger">${falseVal}</span>`
                                        }
                                    }
                                    return data;
                                }
                                break;
                            case 'importstatus':
                                renderFunc = function (data, type, row) {
                                    if (type === 'display' || type === 'filter') {
                                        let saveVal = column.saveVal ?? 'Dapat Disimpan';
                                        let updateVal = column.updateVal ?? 'Update';
                                        let falseVal = column.falseVal ?? 'Tidak Dapat Disimpan';
                                        if (data === "1" || data === 1 || data === true) {
                                            return `<span class="badge px-2 rounded-pill bg-label-success">${saveVal}</span>`;
                                        } else if (data === "2" || data === 2) {
                                            return `<span class="badge px-2 rounded-pill bg-label-warning">${updateVal}</span>`;
                                        } else if (data === "0" || data === 0 || data === false) {
                                            return `<span class="badge px-2 rounded-pill bg-label-danger">${falseVal}</span>`;
                                        }
                                    }
                                    return data;
                                }
                                break;
                            case 'checkbox':
                                renderFunc = function (data, type, row) {
                                    if (type === 'display' || type === 'filter') {
                                        let name = column.selectName ? column.selectName : 'checkbox';
                                        return `<input type="checkbox" class="dt-checkboxes form-check-input" name="${column.selectName ? column.selectName : 'checkbox'}[]" value="${data}">`;
                                    }
                                    return data;
                                }
                                break;
                            case 'switch':
                                renderFunc = function (data, type, row) {
                                    const isActive = data === 1 || data === '1' || data === true;
                                    const trueVal = column.trueVal ?? 'Aktif';
                                    const falseVal = column.falseVal ?? 'Nonaktif';
                                    const label = isActive ? trueVal : falseVal;
                                    const itemId = row.item_id ?? row.idincrement ?? '';

                                    if (type === 'export' || type === 'filter') {
                                        return label;
                                    }

                                    if (type === 'display') {
                                        const checked = isActive ? 'checked' : '';
                                        const stateClass = isActive ? 'is-active' : 'is-inactive';
                                        return `
                                            <div class="dt-switch-wrap ${stateClass}">
                                                <label class="dt-switch mb-0">
                                                    <input type="checkbox" class="dt-status-switch" data-id="${itemId}" ${checked}>
                                                    <span class="dt-switch-slider"></span>
                                                </label>
                                                <span class="dt-switch-label">${label}</span>
                                            </div>
                                        `;
                                    }

                                    return data;
                                }
                                break;
                            case 'input':
                                //for text/number only
                                renderFunc = function (data, type, row) {
                                    if (type === 'display' || type === 'filter') {

                                        let attributes = [
                                            `type="${column.inputType ?? 'text'}"`,
                                            `placeholder="${column.inputPlaceholder ?? $column.name}"`,
                                            `name="${column.inputName ?? `input[${column.name}]`}"`,
                                            `class="${column.inputClass ?? 'form-control'}"`,
                                        ];

                                        const nameLength = column.inputPlaceholder ?? $column.name;

                                        if (nameLength.length > 0) {
                                            attributes.push(`style="width: 218.938px;"`)
                                        }

                                        if (column.inputReadonly === true) {
                                            attributes.push(`readonly`);
                                        }

                                        if (column.inputDisabled === true) {
                                            attributes.push(`disabled`);
                                        }

                                        if (Number.isInteger(column.inputMin)) {
                                            attributes.push(`min="${column.inputMin}"`);
                                        }

                                        if (Number.isInteger(column.inputMax)) {
                                            attributes.push(`max="${column.inputMax}"`);
                                        }

                                        return `<input ${attributes.join(' ')}>`;
                                    }
                                    return data;
                                }
                                break;
                            case "array":
                                renderFunc = function (data, type, row) {
                                    if (!data) return "";

                                    const parsed = parseArrayForRow(data, column.currency);

                                    // UI rendering (HTML list)
                                    if (type === 'display') {
                                        return `<ul style="padding-left:16px; margin:0;">${parsed}</ul>`;
                                    }

                                    // Export rendering (plain text)
                                    if (type === 'export') {
                                        // Convert <li> to readable lines
                                        return parsed
                                            .replace(/<li>/g, '• ')
                                            .replace(/<\/li>/g, '\n')
                                            .replace(/<[^>]*>/g, ''); // strip any remaining HTML
                                    }

                                    // Default fallback (filter/sort)
                                    return parsed.replace(/<[^>]*>/g, '');
                                };
                                break;
                            case "arraykey":
                                renderFunc = function (data, type, row) {
                                    const arr = column.array ?? [];
                                    if (arr.length === 0) return "";
                                    const defaultValue =
                                        column.defaultValue ?? "";
                                    const key = column.arrayKey ?? "id";
                                    const value = column.arrayValue ?? "val";
                                    const item = arr.find(
                                        (obj) => obj[key] === data,
                                    );

                                    return item ? item[value] : defaultValue;
                                };
                                break;
                            case 'nova_edit':
                                renderFunc = function (data, type, row) {
                                    if (type === 'display' || type === 'filter') {
                                        const va = data ?? '-';
                                        const custid = row.CUSTID ?? row.custid ?? '';
                                        const nis = row.nocust ?? row.NOCUST ?? '';
                                        return `<span class="me-1">${va}</span>` +
                                            `<button type="button" class="btn btn-sm btn-icon btn-outline-primary btn-edit-nova" ` +
                                            `data-custid="${custid}" data-nis="${nis}" data-nova="${va}" title="Edit Nomor VA">` +
                                            `<i class="ri-pencil-line"></i></button>`;
                                    }
                                    return data;
                                };
                                break;
                            case 'custom_code_tagihan':
                                renderFunc = function (data, type, row) {
                                    // ANDROID hanya dari scctbill.NOREFF = Mobile
                                    const billNoreff = String(row?.BILL_NOREFF ?? '').trim().toLowerCase();
                                    if (billNoreff === 'mobile') {
                                        return 'ANDROID';
                                    }
                                    const descriptions = {
                                        '1140000': 'Manual Cash',
                                        '1140001': 'Manual BMI',
                                        '1140002': 'Manual SALDO',
                                        '1140003': 'Transfer Bank Lain',
                                        '1140004': 'Transfer Bank BNI',
                                        '1140005': 'Transfer Bank BRI',
                                        '1200001': 'Loket Manual - Beasiswa',
                                        '1200002': 'Loket Manual - Potongan',
                                        '1': 'H2H VA BMI - ATM',
                                        '2': 'H2H VA BMI - Teller',
                                        '3': 'H2H VA BMI - IBANK',
                                        '4': 'H2H VA BMI - EDC',
                                        '5': 'H2H VA BMI - MOBILE',
                                        '6': 'ANDROID',
                                        null: 'Nomor VA',
                                        '': 'Nomor VA'
                                    };
                                    return descriptions[data] || data;
                                }
                                break;
                        }
                    } else {
                        renderFunc = function (data, type, row) {
                            if (data === 0 || data === '0') {
                                return data;
                            }
                            if (data === null || data === undefined || data === '') {
                                return '';
                            }
                            return data;
                        }
                    }

                    const isDuplicate = column.duplicate ?? false;
                    const columnDef = {
                        data: column.data,
                        name: column.name,
                        duplicate: isDuplicate,
                        searchable: column.searchable ?? false,
                        orderable: column.orderable ?? false,
                        // orderable: !isDuplicate ? false : (column.orderable ?? false),
                        render: renderFunc ?? false,
                        className: column.className ?? false,
                        search: false,
                        exportable: column.exportable ?? false,
                        visible: column.visible ?? true,
                        excludeFromSelection: column.excludeFromSelection ?? false,
                        columnType: columnType ?? null,
                        numberColumn: column.numberColumn ?? false,
                    };
                    if (column.data === 'FUrutan' || column.data === 'BILLAM') {
                        columnDef.type = 'num';
                    }
                    options.dataColumns.push(columnDef);
                })
                if (options.thead) {
                    createColumns(options.tableId, options.dataColumns, 'thead');
                }
                if (options.tfoot) {
                    prepareTableFoot(options.tableId);
                }
                dataTableCreate(options);
    };

    const prefetched = options.prefetchedColumns;
    if (Array.isArray(prefetched) && prefetched.length > 0) {
        finishColumns(prefetched);
        return;
    }

    if (!options.columnUrl) {
        warningAlert('Data tidak dapat dimuat, silahkan muat ulang halaman');
        return;
    }

    $.ajax({
        url: options.columnUrl,
        success: finishColumns,
            error: function (xhr) {
                if (xhr && (xhr.status === 401 || xhr.status === 419)) {
                    if (typeof window.pingKeepAlive === 'function') {
                        window.pingKeepAlive();
                    }
                    return;
                }
                const descriptions = {
                    403: 'Anda tidak memiliki izin untuk mengakses kolom data.',
                    404: 'Endpoint kolom data tidak ditemukan.',
                    500: 'Gagal memuat definisi kolom tabel.',
                };
                errorAlert(descriptions[xhr.status] || 'Gagal memuat kolom tabel. Silahkan muat ulang halaman.');
            }
        });
}


function mergeTableRows(tableSelector, columnIndex) {
    const table = $(tableSelector);
    let prevCell = null;
    let rowspan = 1;

    table.find(`tbody tr`).each(function () {
        const currentCell = $(this).find(`td:eq(${columnIndex})`);
        if (prevCell && currentCell.text() === prevCell.text()) {
            currentCell.hide();
            prevCell.attr('rowspan', ++rowspan);
        } else {
            prevCell = currentCell;
            rowspan = 1;
        }
    });
}

function parseArrayForRow(data, currency = false) {
    return Object.entries(data)
        .map(([key, value]) => {
            const keyLabel = key
                .replace(/_/g, " ")
                .replace(/\b\w/g, (c) => c.toUpperCase());
            const formattedValue =
                currency === true
                    ? "Rp. " +
                    value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                    : value;
            return `<li>${formattedValue}</li>`;
        })
        .join("");
}
