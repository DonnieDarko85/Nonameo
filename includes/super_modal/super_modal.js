function minimizzaModal(id) {
    const modal = document.getElementById(id);
    modal.style.display = 'none';

    let icon = document.getElementById(id + 'Icon');
    if (!icon) {
        icon = document.createElement('div');
        icon.id = id + 'Icon';
        icon.innerText = modal.dataset.icon || 'Modale';
        icon.style.position = 'fixed';
        icon.style.bottom = '10px';
        icon.style.right = '10px';
        icon.style.cursor = 'pointer';
        icon.style.zIndex = '1001';
        icon.style.background = '#111';
        icon.style.color = '#ccc';
        icon.style.padding = '5px 10px';
        icon.style.fontFamily = 'Bitter';
        document.body.appendChild(icon);
        icon.onclick = function () {
            modal.style.display = 'block';
            icon.remove();
        };
    }
}

function makeDraggable(modalId) {
    const modal = document.getElementById(modalId);
    const handle = document.getElementById(modalId + '_header');
    let pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;

    handle.onmousedown = function (e) {
        e = e || window.event;
        e.preventDefault();
        pos3 = e.clientX;
        pos4 = e.clientY;
        document.onmouseup = closeDragElement;
        document.onmousemove = elementDrag;
    };

    function elementDrag(e) {
        e = e || window.event;
        e.preventDefault();
        pos1 = pos3 - e.clientX;
        pos2 = pos4 - e.clientY;
        pos3 = e.clientX;
        pos4 = e.clientY;
        modal.style.top = (modal.offsetTop - pos2) + "px";
        modal.style.left = (modal.offsetLeft - pos1) + "px";
    }

    function closeDragElement() {
        document.onmouseup = null;
        document.onmousemove = null;
    }
}
