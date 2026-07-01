<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Services\ReportService;
use CodeIgniter\HTTP\ResponseInterface;

class ReportController extends BaseController
{
    public function index(): ResponseInterface
    {
        $de        = $this->request->getGet('de');
        $ate       = $this->request->getGet('ate');
        $projetoId = $this->request->getGet('projeto_id');

        foreach (['de' => $de, 'ate' => $ate] as $label => $value) {
            if ($value !== null && $value !== '' && ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                return $this->response->setStatusCode(422)->setJSON([
                    'status'  => 'error',
                    'message' => "Parâmetro \"{$label}\" deve estar no formato AAAA-MM-DD.",
                ]);
            }
        }

        if ($projetoId !== null && $projetoId !== '' && ! ctype_digit((string) $projetoId)) {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => 'error',
                'message' => 'Parâmetro "projeto_id" deve ser numérico.',
            ]);
        }

        $de        = ($de === '') ? null : $de;
        $ate       = ($ate === '') ? null : $ate;
        $projetoId = ($projetoId === null || $projetoId === '') ? null : (int) $projetoId;

        $report = (new ReportService())->aggregate($de, $ate, $projetoId);

        return $this->response->setStatusCode(200)->setJSON(array_merge(
            [
                'status'  => 'success',
                'periodo' => ['de' => $de, 'ate' => $ate, 'projeto_id' => $projetoId],
            ],
            $report
        ));
    }
}
