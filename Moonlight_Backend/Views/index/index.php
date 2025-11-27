<div class="container">

    <div class="card">
        <div class="card-header">
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
                echo "<h3 class='white-text text-center'>Olá " . $userName . ", como vai nessa " . $greeting . "?</h3>";
            ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="white-text text-center">Dashboards Administrativas</h4>
        </div>
        <div class="card-body">
            <div class="dashboard_1">
                <h4 class="white-text text-center">
                    Métricas de Vendas e Faturamento: (Financeiro)
                </h4>
                <iframe width="100%" height="600px" src="http://localhost:3000/public/dashboard/590f5d4e-1f45-4b20-8b3c-08b0e8b47d56" frameborder="0"></iframe>
            </div>
            <hr class="white-text">
            <div class="dashboard_2">
                <h4 class="white-text text-center">
                    Métricas de Produto: (Catálogo)
                </h4>
                <iframe width="100%" height="610px" src="http://localhost:3000/public/dashboard/d4869267-c1c4-452e-9290-75e08348b18c" frameborder="0"></iframe>
            </div>
            <hr class="white-text">
            <div class="dashboard_3">
                <h4 class="white-text text-center">
                    Métricas de Usuários: (Comunidade)
                </h4>
                <iframe width="100%" height="600px" src="http://localhost:3000/public/dashboard/1cb75bfa-e424-46ab-a93a-c3b4279b01ed" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>
           