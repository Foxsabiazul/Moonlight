function fecharModal(){
    const modalContainer = document.getElementById('modal-container');
    const modalOverlay = document.getElementById('modal-overlay');
    modalContainer.style.display = "none";
    modalContainer.style.opacity = "0";
    modalOverlay.style.display = "none";
    modalOverlay.style.opacity = "0";

}

window.onload = () =>{
    const modalContainer = document.getElementById('modal-container');
    const modalOverlay = document.getElementById('modal-overlay');
    if(modalContainer){
        modalContainer.style.display = "flex";
        modalOverlay.style.display = "flex";
        modalContainer.style.opacity = "1";
        modalOverlay.style.opacity = "1";
    }
}
