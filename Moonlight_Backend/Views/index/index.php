<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="card-body">
            <?php
                date_default_timezone_set("America/Sao_Paulo");
                $hour = date('H');
                $greeting = "Olá";

                // Define a saudação com base na hora do dia
                if ($hour >= 5 && $hour < 12) {
                    $greeting = "manhã";
                } else if ($hour >= 12 && $hour < 18) {
                    $greeting = "tarde";
                } else {
                    $greeting = "noite";
                }

                $userName = isset($_SESSION['Logado_Na_Sessão']) ? htmlspecialchars($_SESSION['Logado_Na_Sessão']["nm_user"]) : "Usuário";
                echo "<h3 class='white-text'>Seja bem vindo" . $userName . ", como vai nessa " . $greeting . "?</h3>";
            ?>
             </div>
         </div>
    </div>
</div>
           