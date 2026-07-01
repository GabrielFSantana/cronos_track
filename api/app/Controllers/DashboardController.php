<?php

namespace App\Controllers;

use App\Models\ProjetoModel;
use App\Services\ReportService;

class DashboardController extends BaseController
{
    public function index(): string
    {
        $de        = $this->validDateOrNull($this->request->getGet('de'));
        $ate       = $this->validDateOrNull($this->request->getGet('ate'));
        $projetoId = $this->request->getGet('projeto_id');
        $projetoId = ($projetoId !== null && ctype_digit((string) $projetoId)) ? (int) $projetoId : null;

        $report   = (new ReportService())->aggregate($de, $ate, $projetoId);
        $projetos = (new ProjetoModel())->orderBy('nome', 'ASC')->findAll();

        return view('dashboard', [
            'report'   => $report,
            'projetos' => $projetos,
            'filtros'  => ['de' => $de, 'ate' => $ate, 'projeto_id' => $projetoId],
        ]);
    }

    /**
     * Filtro de data inválido na tela é apenas ignorado (cai pra "sem filtro"),
     * diferente da API JSON, que retorna 422.
     */
    private function validDateOrNull(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) ? $value : null;
    }
}
