import * as bootstrap from 'bootstrap';
import * as agGrid from 'ag-grid-community';
import Quill from 'quill';
import Chart from 'chart.js/auto';

import './erp-grid.js';
import './erp-forms.js';

window.bootstrap = bootstrap;
window.agGrid = agGrid;
window.Quill = Quill;
window.Chart = Chart;

/**
 * Universal 1-Line ERP Editor Initializer
 * Usage anywhere in the application:
 *   const editor = window.initErpEditor('#my-editor');
 */
window.initErpEditor = function (target, customOptions = {}) {
    const el = typeof target === 'string' ? document.querySelector(target) : target;
    if (!el) return null;

    if (el.__quillInstance) return el.__quillInstance;

    const quill = new Quill(el, {
        theme: 'snow',
        placeholder: customOptions.placeholder || 'Write here...',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                [{ 'font': [] }, { 'size': ['small', false, 'large', 'huge'] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'script': 'sub'}, { 'script': 'super' }],
                [{ 'align': [] }],
                [{ 'list': 'ordered'}, { 'list': 'bullet'}, { 'list': 'check' }],
                [{ 'indent': '-1'}, { 'indent': '+1' }],
                ['blockquote', 'code-block'],
                ['table', 'link', 'image', 'video'],
                ['clean']
            ],
            table: true,
            ...customOptions.modules
        }
    });

    el.__quillInstance = quill;

    quill.getHTML = function () {
        return quill.root ? quill.root.innerHTML : '';
    };
    quill.setHTML = function (html) {
        if (quill.root) quill.root.innerHTML = html || '';
    };
    quill.clear = function () {
        if (quill.root) quill.root.innerHTML = '';
    };

    attachErpImageResizer(quill);

    return quill;
};

function attachErpImageResizer(quillInstance) {
    const root = quillInstance.root;
    let currentImg = null;
    let resizerOverlay = null;

    function removeOverlay() {
        if (resizerOverlay && resizerOverlay.parentNode) {
            resizerOverlay.parentNode.removeChild(resizerOverlay);
        }
        resizerOverlay = null;
        currentImg = null;
    }

    root.addEventListener('click', (e) => {
        if (e.target && e.target.tagName === 'IMG') {
            createResizerOverlay(e.target);
        } else if (!e.target.closest('#quill-image-resizer-overlay')) {
            removeOverlay();
        }
    });

    function createResizerOverlay(img) {
        removeOverlay();
        currentImg = img;

        resizerOverlay = document.createElement('div');
        resizerOverlay.id = 'quill-image-resizer-overlay';
        resizerOverlay.style.position = 'absolute';
        resizerOverlay.style.border = '2px dashed #2563eb';
        resizerOverlay.style.boxSizing = 'border-box';
        resizerOverlay.style.pointerEvents = 'none';
        resizerOverlay.style.zIndex = '100';

        const toolbar = document.createElement('div');
        toolbar.style.position = 'absolute';
        toolbar.style.top = '-36px';
        toolbar.style.left = '50%';
        toolbar.style.transform = 'translateX(-50%)';
        toolbar.style.background = '#0f172a';
        toolbar.style.color = '#ffffff';
        toolbar.style.padding = '4px 8px';
        toolbar.style.borderRadius = '6px';
        toolbar.style.display = 'flex';
        toolbar.style.alignItems = 'center';
        toolbar.style.gap = '6px';
        toolbar.style.pointerEvents = 'auto';
        toolbar.style.fontSize = '12px';
        toolbar.style.boxShadow = '0 4px 6px -1px rgba(0,0,0,0.2)';

        toolbar.innerHTML = `
            <button type="button" style="background:none;border:none;color:#fff;cursor:pointer;" title="Align Left" id="img-align-left"><i class="bi bi-text-left"></i></button>
            <button type="button" style="background:none;border:none;color:#fff;cursor:pointer;" title="Align Center" id="img-align-center"><i class="bi bi-text-center"></i></button>
            <button type="button" style="background:none;border:none;color:#fff;cursor:pointer;" title="Align Right" id="img-align-right"><i class="bi bi-text-right"></i></button>
            <span style="border-left:1px solid #475569;height:12px;margin:0 2px;"></span>
            <button type="button" style="background:none;border:none;color:#fff;cursor:pointer;" title="25%" id="img-size-25">25%</button>
            <button type="button" style="background:none;border:none;color:#fff;cursor:pointer;" title="50%" id="img-size-50">50%</button>
            <button type="button" style="background:none;border:none;color:#fff;cursor:pointer;" title="100%" id="img-size-100">100%</button>
        `;

        const handle = document.createElement('div');
        handle.style.position = 'absolute';
        handle.style.right = '-6px';
        handle.style.bottom = '-6px';
        handle.style.width = '12px';
        handle.style.height = '12px';
        handle.style.background = '#2563eb';
        handle.style.border = '2px solid #ffffff';
        handle.style.borderRadius = '2px';
        handle.style.cursor = 'nwse-resize';
        handle.style.pointerEvents = 'auto';

        resizerOverlay.appendChild(toolbar);
        resizerOverlay.appendChild(handle);

        const container = root.parentNode;
        container.style.position = 'relative';
        container.appendChild(resizerOverlay);

        function updateOverlayPos() {
            if (!currentImg) return;
            const imgRect = currentImg.getBoundingClientRect();
            const parentRect = container.getBoundingClientRect();

            resizerOverlay.style.top = (imgRect.top - parentRect.top + container.scrollTop) + 'px';
            resizerOverlay.style.left = (imgRect.left - parentRect.left + container.scrollLeft) + 'px';
            resizerOverlay.style.width = imgRect.width + 'px';
            resizerOverlay.style.height = imgRect.height + 'px';
        }

        updateOverlayPos();

        toolbar.querySelector('#img-align-left').onclick = (e) => {
            e.stopPropagation();
            currentImg.style.display = 'block';
            currentImg.style.margin = '0 auto 0 0';
            updateOverlayPos();
        };
        toolbar.querySelector('#img-align-center').onclick = (e) => {
            e.stopPropagation();
            currentImg.style.display = 'block';
            currentImg.style.margin = '0 auto';
            updateOverlayPos();
        };
        toolbar.querySelector('#img-align-right').onclick = (e) => {
            e.stopPropagation();
            currentImg.style.display = 'block';
            currentImg.style.margin = '0 0 0 auto';
            updateOverlayPos();
        };
        toolbar.querySelector('#img-size-25').onclick = (e) => {
            e.stopPropagation();
            currentImg.style.width = '25%';
            currentImg.style.height = 'auto';
            updateOverlayPos();
        };
        toolbar.querySelector('#img-size-50').onclick = (e) => {
            e.stopPropagation();
            currentImg.style.width = '50%';
            currentImg.style.height = 'auto';
            updateOverlayPos();
        };
        toolbar.querySelector('#img-size-100').onclick = (e) => {
            e.stopPropagation();
            currentImg.style.width = '100%';
            currentImg.style.height = 'auto';
            updateOverlayPos();
        };

        let startX, startWidth;
        handle.onmousedown = (e) => {
            e.preventDefault();
            e.stopPropagation();
            startX = e.clientX;
            startWidth = currentImg.clientWidth;

            function onMouseMove(moveEvent) {
                const parentW = container.clientWidth || 800;
                const newWidth = Math.min(parentW, Math.max(50, startWidth + (moveEvent.clientX - startX)));
                currentImg.style.width = newWidth + 'px';
                currentImg.style.height = 'auto';
                updateOverlayPos();
            }

            function onMouseUp() {
                document.removeEventListener('mousemove', onMouseMove);
                document.removeEventListener('mouseup', onMouseUp);
            }

            document.addEventListener('mousemove', onMouseMove);
            document.addEventListener('mouseup', onMouseUp);
        };
    }
}

