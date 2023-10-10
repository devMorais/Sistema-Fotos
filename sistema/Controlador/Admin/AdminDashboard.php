<?php

namespace sistema\Controlador\Admin;

/**
 * Classe - Dashboard do painel administrativo.
 *
 * @author Fernando
 */
class AdminDashboard extends AdminControlador
{

    public function dashboard(): void
    {
        echo $this->template->renderizar('dashboard.html', [
        ]);
    }
}
