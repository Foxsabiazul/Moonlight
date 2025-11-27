function fecharModal(){
    const modalcontainer = document.getElementById('modal-container');
    const modaloverlay = document.getElementById('modal-overlay');
    modalcontainer.style.display = "none";
    modalcontainer.style.opacity = "0";
    modaloverlay.style.display = "none";
    modaloverlay.style.opacity = "0";
}

function fecharModalExclusao(){
    const modalContainer = document.getElementById('modalContainer');
    const modalOverlay = document.getElementById('modalOverlay');
    modalContainer.style.display = "none";
    modalContainer.style.opacity = "0";
    modalOverlay.style.display = "none";
    modalOverlay.style.opacity = "0";
}


window.onload = () =>{
    const modalcontainer = document.getElementById('modal-container');
    const modaloverlay = document.getElementById('modal-overlay');
    if(modalcontainer){
        modalcontainer.style.display = "flex";
        modaloverlay.style.display = "flex";
        modalcontainer.style.opacity = "1";
        modaloverlay.style.opacity = "1";
    }
}
