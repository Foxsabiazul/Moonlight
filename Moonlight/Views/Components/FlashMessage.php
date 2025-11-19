<?php if(isset($_SESSION['modalMessage'])): ?>
        <div id="modal-container" class="modal-container" style="display: flex; opacity: 1;">
            <div class="modal">
                <div class="modalContainerTitle">
                    <h3 class="modalTitle">
                        <?= htmlspecialchars($_SESSION['modalTitle']) ?>
                    </h3>
                </div>
                <div class="modalContainerMessage">
                    <p class="modalMessage">
                        <?= htmlspecialchars($_SESSION['modalMessage']) ?>
                    </p>
                </div>
                 <div class="modalButton">
                    <button onclick="fecharModal()">
                        Fechar
                    </button>
                </div>
            </div>
        </div>
        <div id="modal-overlay" class="modal-overlay" style="display: flex; opacity: 1;"></div>
        <?php unset($_SESSION['modalTitle']); ?>
        <?php unset($_SESSION['modalMessage']); ?>
<?php endif; ?>