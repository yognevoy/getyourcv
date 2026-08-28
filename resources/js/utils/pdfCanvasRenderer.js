import * as pdfjsLib from 'pdfjs-dist';
import pdfjsWorker from 'pdfjs-dist/build/pdf.worker.min.mjs?url';

pdfjsLib.GlobalWorkerOptions.workerSrc = pdfjsWorker;

export async function renderPdfContent(data, width) {
    const doc = await pdfjsLib.getDocument({ data: new Uint8Array(data) }).promise;
    const canvases = [];

    // Supersample beyond devicePixelRatio, 1:1 looks soft.
    const outputScale = (window.devicePixelRatio || 1) * 1.5;

    for (let pageNumber = 1; pageNumber <= doc.numPages; pageNumber++) {
        const page = await doc.getPage(pageNumber);
        const scale = width / page.getViewport({ scale: 1 }).width;
        const viewport = page.getViewport({ scale });

        const canvas = document.createElement('canvas');
        canvas.width = Math.floor(viewport.width * outputScale);
        canvas.height = Math.floor(viewport.height * outputScale);
        canvas.style.width = `${Math.floor(viewport.width)}px`;
        canvas.style.height = `${Math.floor(viewport.height)}px`;
        canvas.className = 'border border-ink/10';

        const transform = [outputScale, 0, 0, outputScale, 0, 0];

        await page.render({ canvasContext: canvas.getContext('2d'), viewport, transform }).promise;
        canvases.push(canvas);
    }

    return canvases;
}
